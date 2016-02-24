<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin utils.
 *
 * @since 16xxxx WP notices.
 */
class Plugins extends CoreClasses\AppBase
{
    /**
     * All active plugins.
     *
     * @since 16xxxx First documented version.
     *
     * @return array All active plugins.
     */
    public function active(): array
    {
        if (!is_null($active = &$this->cacheKey(__FUNCTION__))) {
            return $active;
        }
        if (!is_array($active = get_option('active_plugins'))) {
            $active = []; // Fprce an array.
        }
        if (is_multisite()) {
            if (is_array($active_sitewide = get_site_option('active_sitewide_plugins'))) {
                $active_sitewide = array_keys($active_sitewide);
                $active          = array_merge($active, $active_sitewide);
            }
        }
        return array_unique($active);
    }
}
