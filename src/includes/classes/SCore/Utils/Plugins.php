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
 * Plugin utils.
 *
 * @since 16xxxx WP notices.
 */
class Plugins extends Classes\SCore\Base\Core
{
    /**
     * All active plugins.
     *
     * @since 16xxxx First documented version.
     *
     * @return array All active plugins.
     */
    public function allActive(): array
    {
        if (!is_array($active = get_option('active_plugins'))) {
            $active = []; // Fprce an array.
        }
        if (is_multisite()) {
            if (is_array($network_active = get_network_option(null, 'active_sitewide_plugins'))) {
                $network_active = array_keys($network_active);
                $active         = array_merge($active, $network_active);
            }
        }
        return array_unique($active);
    }
}
