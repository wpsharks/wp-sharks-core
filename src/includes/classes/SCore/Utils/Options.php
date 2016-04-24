<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Option utils.
 *
 * @since 16xxxx Option utils.
 */
class Options extends Classes\SCore\Base\Core
{
    /**
     * Save action.
     *
     * @since 16xxxx Option utils.
     *
     * @type string Save action.
     */
    protected $save_action;

    /**
     * Restore defaults action.
     *
     * @since 16xxxx Option utils.
     *
     * @type string Restore defaults action.
     */
    protected $restore_defaults_action;

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->save_action             = $this->App->Config->©brand['©var'].'_save_options';
        $this->restore_defaults_action = $this->App->Config->©brand['©var'].'_restore_default_options';
    }

    /**
     * Save submit URL.
     *
     * @since 16xxxx Option utils.
     *
     * @return string Save submit URL.
     */
    public function saveUrl(): string
    {
        $action = $this->save_action;

        $url = $this->c::currentUrl();
        $url = $this->s::addUrlNonce($url, $action);

        return $url;
    }

    /**
     * Save form element ID.
     *
     * @since 16xxxx Option utils.
     *
     * @param string $key Option key.
     *
     * @return string Save form element ID.
     */
    public function formElementId(string $key): string
    {
        return $this->App->Config->©brand['©slug'].'-option-'.$key;
    }

    /**
     * Save form element name.
     *
     * @since 16xxxx Option utils.
     *
     * @param string $key Option key.
     *
     * @return string Save form element name.
     */
    public function formElementName(string $key): string
    {
        return $this->save_action.'['.$key.']';
    }

    /**
     * Maybe save options.
     *
     * @since 16xxxx Option utils.
     */
    public function onAdminInitMaybeSave()
    {
        $action = $this->save_action;

        if (empty($_REQUEST[$action])) {
            return; // Nothing to do.
        }
        $this->c::noCacheHeaders();
        $this->s::requireValidNonce($action);

        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden();
        }
        $options = $this->c::unslash($_REQUEST[$action]);
        $options = $this->c::mbTrim($options);
        $this->update($options);

        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlNonce($url);

        $markup = __('%1$s options updated successfully.', 'wp-sharks-core');
        $markup = sprintf($markup, esc_html($this->App->Config->©brand['©name']));
        $this->s::enqueueUserNotice($markup, ['type' => 'success']);

        wp_redirect($url);
        exit; // Stop.
    }

    /**
     * Restore defaults.
     *
     * @since 16xxxx Option utils.
     */
    public function restoreDefaults()
    {
        $this->update($this->App->Config->§default_options);
    }

    /**
     * Restore default options URL.
     *
     * @since 16xxxx Option utils.
     *
     * @return string Restore default options URL.
     */
    public function restoreDefaultsUrl(): string
    {
        $action = $this->restore_defaults_action;

        $url = $this->c::currentUrl();
        $url = $this->c::addUrlQueryArgs([$action => ''], $url);
        $url = $this->s::addUrlNonce($url, $action);

        return $url;
    }

    /**
     * Maybe restore default options.
     *
     * @since 16xxxx Option utils.
     */
    public function onAdminInitMaybeRestoreDefaults()
    {
        $action = $this->restore_defaults_action;

        if (!isset($_REQUEST[$action])) {
            return; // Nothing to do.
        }
        $this->c::noCacheHeaders();
        $this->s::requireValidNonce($action);

        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden();
        }
        $this->restoreDefaults();

        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlNonce($url);
        $url = $this->c::removeUrlQueryArgs([$action], $url);

        wp_redirect($url);
        exit; // Stop.
    }

    /**
     * Get config option.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $key Option key.
     *
     * @throws Exception On unknown key.
     *
     * @return mixed|null Option value.
     */
    public function get(string $key)
    {
        if (isset($this->App->Config->§options[$key])) {
            return $this->App->Config->§options[$key];
        } // Optimized. Values CAN be `null` however.

        // Else if the key does not exist at all, throw exception.
        if (!array_key_exists($key, $this->App->Config->§options)) {
            throw new Exception(sprintf('Unknown option key: `%1$s`.', $key));
        }
        return null; // Default return value.
    }

    /**
     * Update config options.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $options Options to update.
     *
     * @note `null` options force a default value.
     */
    public function update(array $options)
    {
        $this->App->Config->§options = $this->merge($this->App->Config->§options, $options);
        $this->App->Config->§options = $this->s::applyFilters('options', $this->App->Config->§options);

        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            update_network_option(null, $this->App->Config->©brand['©var'].'_options', $this->App->Config->§options);
        } else {
            update_option($this->App->Config->©brand['©var'].'_options', $this->App->Config->§options);
        }
    }

    /**
     * Merge options.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The resuling array after merging.
     *
     * @note `null` options force a default value.
     */
    public function merge(array $base, array $merge): array
    {
        $options = array_merge($base, $merge);
        $options = array_intersect_key($options, $this->App->Config->§default_options);

        foreach ($this->App->Config->§default_options as $_key => $_default_option_value) {
            if (is_null($options[$_key])) {
                $options[$_key] = $_default_option_value;
            } elseif (!$this->App->Config->§specs['§is_pro'] && in_array($_key, $this->App->Config->§pro_option_keys, true)) {
                $options[$_key] = $_default_option_value;
            } else {
                settype($options[$_key], gettype($_default_option_value));
            }
        } // unset($_key, $_default_option_value);

        return $options;
    }
}
