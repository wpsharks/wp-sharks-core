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
 * @since 160524 WP notices.
 */
class Plugin extends Classes\SCore\Base\Core
{
    /**
     * Plugin slug from basename.
     *
     * @since 160524 First documented version.
     *
     * @param string $basename Plugin basename.
     *
     * @return string Plugin slug or an empty string.
     */
    public function slug(string $basename): string
    {
        if (($slug = mb_strstr($basename, '/', true))) {
            return $slug; // Got what we need here.
        } elseif (($slug = mb_strstr($basename, '.php', true))) {
            return $slug; // Got what we need here.
        }
        return ''; // Failed on `[slug]/plugin.php` & `[slug].php`.
    }

    /**
     * Plugin slugs from basenames.
     *
     * @since 160524 First documented version.
     *
     * @param array $basenames Plugin basenames.
     *
     * @return array Plugin slugs w/ basename keys.
     */
    public function slugs(array $basenames): array
    {
        $slugs = []; // Initialize.

        foreach ($basenames as $_basename) {
            if (($_slug = $this->slug((string) $_basename))) {
                $slugs[$_basename] = $_slug;
            }
        } // unset($_basename); // Housekeeping.

        return $slugs;
    }

    /**
     * Is a plugin installed?
     *
     * @since 160524 First documented version.
     *
     * @param string $slug Plugin slug.
     *
     * @return bool True if the plugin is installed.
     */
    public function isInstalled(string $slug): bool
    {
        return $this->installedData($slug, 'version') ? true : false;
    }

    /**
     * Installed plugin data from slug.
     *
     * @since 160524 First documented version.
     *
     * @param string $slug Plugin slug.
     * @param string $key  Data key to acquire.
     *
     * @return string Plugin data key; always a string.
     */
    public function installedData(string $slug, string $key): string
    {
        if (!$slug || !$key) { // Either empty?
            return ''; // Not possible.
        }
        switch ($key) {
            case 'basename':
                $key = 'Basename';
                break;
            case 'name':
                $key = 'Name';
                break;
            case 'version':
                $key = 'Version';
                break;
            case 'description':
                $key = 'Description';
                break;
            case 'url':
                $key = 'PluginURI';
                break;
            case 'text_domain':
                $key = 'TextDomain';
                break;
            case 'author':
                $key = 'Author';
                break;
            case 'author_url':
                $key = 'AuthorURI';
                break;
            default: // Default case.
                return ''; // Unknown key.
        }
        $installed_plugins = $this->s::allInstalledPlugins(); // « Cached statically.
        return !empty($installed_plugins[$slug][$key]) ? (string) $installed_plugins[$slug][$key] : '';
    }
}
