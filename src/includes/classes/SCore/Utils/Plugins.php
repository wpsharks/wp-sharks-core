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
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Plugin utils.
 *
 * @since 16xxxx WP notices.
 */
class Plugins extends Classes\SCore\Base\Core
{
    /**
     * Global static cache.
     *
     * @since 16xxxx First documented version.
     *
     * @type array Global static cache.
     */
    protected static $cache; // All WPSC usage.

    /**
     * Active plugins.
     *
     * @since 16xxxx First documented version.
     *
     * @param bool $slugify Slugs w/ basename keys?
     * @note If false you'll get numerically indexed basenames.
     *
     * @return array Active plugins.
     */
    public function active(bool $slugify = true): array
    {
        global $blog_id; // Blog ID.

        $cache_key = '_'.(int) $blog_id.(int) $slugify;

        if (isset(static::$cache[__FUNCTION__][$cache_key])) {
            return static::$cache[__FUNCTION__][$cache_key];
        }
        $active = get_option('active_plugins');
        $active = is_array($active) ? $active : [];

        if ($slugify) { // Slugs w/ basename keys.
            $active = $this->s::pluginSlugs($active);
        }
        return static::$cache[__FUNCTION__][$cache_key] = $active;
    }

    /**
     * Network-active plugins.
     *
     * @since 16xxxx First documented version.
     *
     * @param bool $slugify Slugs w/ basename keys?
     * @note If false you'll get numerically indexed basenames.
     *
     * @return array Network-active plugin basenames.
     */
    public function networkActive(bool $slugify = true): array
    {
        $cache_key = '_'.(int) $slugify; // Network wide.

        if (isset(static::$cache[__FUNCTION__][$cache_key])) {
            return static::$cache[__FUNCTION__][$cache_key];
        }
        if (!is_multisite()) { // Not applicable.
            return static::$cache[__FUNCTION__][$cache_key] = [];
        }
        $network_active = get_network_option(null, 'active_sitewide_plugins');
        $network_active = is_array($network_active) ? $network_active : [];
        $network_active = array_keys($network_active);

        if ($slugify) { // Slugs w/ basename keys.
            $network_active = $this->s::pluginSlugs($network_active);
        }
        return static::$cache[__FUNCTION__][$cache_key] = $network_active;
    }

    /**
     * All active plugins.
     *
     * @since 16xxxx First documented version.
     *
     * @param bool $slugify Slugs w/ basename keys?
     * @note If false you'll get numerically indexed basenames.
     *
     * @return array All active plugin basenames.
     */
    public function allActive(bool $slugify = true): array
    {
        global $blog_id; // Blog ID.

        $cache_key = '_'.(int) $blog_id.(int) $slugify;

        if (isset(static::$cache[__FUNCTION__][$cache_key])) {
            return static::$cache[__FUNCTION__][$cache_key];
        }
        $active         = $this->active($slugify);
        $network_active = $this->networkActive($slugify);
        $all_active     = array_merge($active, $network_active);

        if (!$slugify) { // Numeric keys.
            $all_active = array_unique($all_active);
        }
        return static::$cache[__FUNCTION__][$cache_key] = $all_active;
    }

    /**
     * All installed plugins (directory scan).
     *
     * @since 16xxxx First documented version.
     *
     * @param bool $slugify Keyed by slug?
     *
     * @return array All installed plugins (keyed by plugin basename).
     */
    public function allInstalled(bool $slugify = true): array
    {
        $cache_key = '_'.(int) $slugify; // Network wide.

        if (isset(static::$cache[__FUNCTION__][$cache_key])) {
            return static::$cache[__FUNCTION__][$cache_key];
        }
        // Contains the `get_plugins()` function.
        require_once ABSPATH.'wp-admin/includes/plugin.php';

        if (is_admin()) { // Typical use case.
            $installed_plugins = apply_filters('all_plugins', get_plugins());
        } else { // Abnormal use case; no filter here.
            $installed_plugins = get_plugins(); // See: <http://jas.xyz/1NN5zhk>
            // No filter; because it may trigger routines expecting other admin functions
            // that will not exist in this context. That could lead to fatal errors.
        }
        $installed_plugins = is_array($installed_plugins) ? $installed_plugins : [];

        if ($slugify) { // Key by slug.
            $installed_plugins_ = $installed_plugins;
            $installed_plugins  = []; // Reinitialize.
            foreach ($installed_plugins_ as $_basename => $_data) {
                if (($_slug = $this->s::pluginSlug($_basename))) {
                    $installed_plugins[$_slug]             = $_data;
                    $installed_plugins[$_slug]['Basename'] = $_basename;
                }
            } // unset($installed_plugins_, $_basename, $_data); // Housekeeping.
        }
        return static::$cache[__FUNCTION__][$cache_key] = $installed_plugins;
    }
}
