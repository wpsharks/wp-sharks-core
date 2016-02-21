<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Option utils.
 *
 * @since 16xxxx DB utils.
 */
class Options extends WCoreClasses\PluginBase
{
    /**
     * Restore default options.
     *
     * @since 16xxxx Initial release.
     */
    public function restoreDefaults()
    {
        $Config = $this->Plugin->Config;

        $Config->updateOptions($Config->default_options);
    }

    /**
     * Restore default options.
     *
     * @since 16xxxx Initial release.
     */
    protected function restoreDefaultsAction()
    {
        $Config = $this->Plugin->Config;

        return $Config->brand['base_var'].'_restore_default_options';
    }

    /**
     * Restore default options URL.
     *
     * @since 16xxxx Initial release.
     *
     * @return string Restore default options URL.
     */
    public function restoreDefaultsUrl(): string
    {
        $url    = c\current_url();
        $action = $this->restoreDefaultsAction();
        $url    = c\add_url_query_args([$action => ''], $url);
        $url    = wc\add_url_nonce($url, $action);

        return $url;
    }

    /**
     * Maybe restore default options.
     *
     * @since 16xxxx Initial release.
     *
     * @attaches-to `admin_init` action.
     */
    public function onAdminInitMaybeRestoreDefaults()
    {
        $Config = $this->Plugin->Config;
        $action = $this->restoreDefaultsAction();

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
