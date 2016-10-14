<?php
/**
 * Option utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Option utils.
 *
 * @since 160524 Option utils.
 */
class Options extends Classes\SCore\Base\Core
{
    /**
     * Class constructor.
     *
     * @since 160524 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);
    }

    /**
     * Save submit URL.
     *
     * @since 160524 Option utils.
     *
     * @return string Save submit URL.
     */
    public function saveUrl(): string
    {
        return $this->s::restActionUrl('§save-options');
    }

    /**
     * Save options action handler.
     *
     * @since 160524 Option utils.
     */
    public function onRestActionSaveOptions()
    {
        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden(); // Not allowed!
        }
        $this->update((array) $this->s::restActionData('', true));

        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlRestAction($url);

        $markup = __('\'%1$s\' options updated successfully.', 'wp-sharks-core');
        $markup = sprintf($markup, esc_html($this->App->Config->©brand['©name']));
        $this->s::enqueueUserNotice($markup, ['type' => 'success']);

        wp_redirect($url).exit(); // Stop on redirection.
    }

    /**
     * Save via AJAX URL.
     *
     * @since 160531 Option utils.
     *
     * @return string Save via AJAX URL.
     */
    public function saveViaAjaxUrl(): string
    {
        return $this->s::restActionUrl('ajax.§save-options');
    }

    /**
     * Save options via AJAX action handler.
     *
     * @since 160531 Option utils.
     */
    public function onAjaxRestActionSaveOptions()
    {
        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden(); // Not allowed!
        }
        $this->update((array) $this->s::restActionData('', true));

        exit(json_encode(['success' => true]));
    }

    /**
     * Restore defaults.
     *
     * @since 160524 Option utils.
     */
    public function restoreDefaults()
    {
        $this->update($this->App->Config->§default_options);
    }

    /**
     * Restore default options URL.
     *
     * @since 160524 Option utils.
     *
     * @return string Restore default options URL.
     */
    public function restoreDefaultsUrl(): string
    {
        return $this->s::restActionUrl('§restore-default-options');
    }

    /**
     * Restore default options action handler.
     *
     * @since 160524 Option utils.
     */
    public function onRestActionRestoreDefaultOptions()
    {
        if (!current_user_can($this->App->Config->§caps['§manage'])) {
            $this->s::dieForbidden(); // Not allowed!
        }
        $this->restoreDefaults();

        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlRestAction($url);

        $markup = __('Default options for \'%1$s\' restored successfully.', 'wp-sharks-core');
        $markup = sprintf($markup, esc_html($this->App->Config->©brand['©name']));
        $this->s::enqueueUserNotice($markup, ['type' => 'success']);

        wp_redirect($url).exit(); // Stop on redirection.
    }

    /**
     * Get config option.
     *
     * @since 160524 Initial release.
     *
     * @param string $key Option key.
     *
     * @return mixed Option value.
     */
    public function get(string $key)
    {
        if (isset($this->App->Config->§options[$key])) {
            return $this->App->Config->§options[$key];
        } // Optimized. Values CAN be `null` however.

        // Else if the key does not exist at all, throw exception.
        if (!array_key_exists($key, $this->App->Config->§options)) {
            throw $this->c::issue(sprintf('Unknown option key: `%1$s`.', $key));
        }
        return null; // Default return value.
    }

    /**
     * Get default config option.
     *
     * @since 160826 Initial release.
     *
     * @param string $key Option key.
     *
     * @return mixed Default option value.
     */
    public function getDefault(string $key)
    {
        if (isset($this->App->Config->§default_options[$key])) {
            return $this->App->Config->§default_options[$key];
        } // Optimized. Values CAN be `null` however.

        // Else if the key does not exist at all, throw exception.
        if (!array_key_exists($key, $this->App->Config->§default_options)) {
            throw $this->c::issue(sprintf('Unknown default option key: `%1$s`.', $key));
        }
        return null; // Default return value.
    }

    /**
     * Update config options.
     *
     * @since 160524 Initial release.
     *
     * @param array $options Options to update.
     *
     * @internal `null` options force a default value.
     */
    public function update(array $options)
    {
        $this->App->Config->§options = $this->merge($this->App->Config->§options, $options);
        $this->App->Config->§options = $this->s::applyFilters('options', $this->App->Config->§options);
        $this->s::sysOption('options', $this->App->Config->§options);

        if ($this->Wp->is_admin) { // Limit this to admin contexts.
            flush_rewrite_rules(); // In case of options that alter permalinks.
        }
        if ($this->App->Config->§options['§license_key']) {
            $this->s::dequeueNotice('§license-key-request');
        }
        $this->s::dequeueNotice('§license-key-error');
    }

    /**
     * Merge options.
     *
     * @since 160524 Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The resuling array after merging.
     *
     * @internal `null` options force a default value.
     */
    public function merge(array $base, array $merge): array
    {
        $options = array_merge($base, $merge); // Base should include all existing.
        $options = array_intersect_key($options, $this->App->Config->§default_options);

        foreach ($this->App->Config->§default_options as $_key => $_default_option_value) {
            if (!isset($options[$_key])) {
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
