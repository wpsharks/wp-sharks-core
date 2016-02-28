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
class Options extends CoreClasses\Core\Base\Core
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
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $Config = $this->Plugin->Config;

        $this->save_action             = $Config->brand['base_var'].'_save_options';
        $this->restore_defaults_action = $Config->brand['base_var'].'_restore_default_options';
    }

    /**
     * Save submit URL.
     *
     * @since 16xxxx Option utils.
     *
     * @return string Save submit URL.
     */
    public function saveSubmitUrl(): string
    {
        $url    = c\current_url();
        $action = $this->save_action;
        $url    = wc\add_url_nonce($url, $action);

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
    public function saveFormElementId(string $key): string
    {
        $Config = $this->Plugin->Config;

        return $Config->brand['base_slug'].'-option-'.$key;
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
    public function saveFormElementName(string $key): string
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
        $Config  = $this->Plugin->Config;
        $Notices = $this->Plugin->Utils->Notices;
        $action  = $this->save_action;

        if (empty($_REQUEST[$action])) {
            return; // Nothing to do.
        }
        c\no_cache_headers();
        wc\require_valid_nonce($action);

        if (!current_user_can($Config->caps['manage'])) {
            wc\die_forbidden();
        }
        $options = c\mb_trim(c\unslash($_REQUEST[$action]));
        $Config->updateOptions($options);

        $url = c\current_url();
        $url = wc\remove_url_nonce($url);

        $markup = __('%1$s options updated successfully.');
        $markup = sprintf($markup, esc_html($Config->brand['base_name']));
        $Notices->uEnqueue($markup, ['type' => 'success']);

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
        $Config = $this->Plugin->Config;

        $Config->updateOptions($Config->default_options);
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
        $url    = c\current_url();
        $action = $this->restore_defaults_action;
        $url    = c\add_url_query_args([$action => ''], $url);
        $url    = wc\add_url_nonce($url, $action);

        return $url;
    }

    /**
     * Maybe restore default options.
     *
     * @since 16xxxx Option utils.
     */
    public function onAdminInitMaybeRestoreDefaults()
    {
        $Config = $this->Plugin->Config;
        $action = $this->restore_defaults_action;

        if (!isset($_REQUEST[$action])) {
            return; // Nothing to do.
        }
        c\no_cache_headers();
        wc\require_valid_nonce($action);

        if (!current_user_can($Config->caps['manage'])) {
            wc\die_forbidden();
        }
        $this->restoreDefaultOptions();

        $url = c\current_url();
        $url = wc\remove_url_nonce($url);
        $url = c\remove_url_query_args([$action], $url);

        wp_redirect($url);
        exit; // Stop.
    }
}
