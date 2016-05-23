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
 * Theme utils.
 *
 * @since 16xxxx WP notices.
 */
class Theme extends Classes\SCore\Base\Core
{
    /**
     * Is a theme installed?
     *
     * @since 16xxxx First documented version.
     *
     * @param string $slug Theme slug.
     *
     * @return bool True if the theme is installed.
     */
    public function isInstalled(string $slug): bool
    {
        return $this->installedData($slug, 'version') ? true : false;
    }

    /**
     * Installed theme data from slug.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $slug   Theme slug.
     * @param string $key    Data key to acquire.
     * @param bool   $parent Get parent data key?
     *
     * @return string Theme data key; always a string.
     */
    public function installedData(string $slug, string $key, bool $parent = false): string
    {
        if (!$slug || !$key) {
            return ''; // Not possible.
        }
        switch ($key) {
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
                $key = 'ThemeURI';
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
        if (($installed_theme = &$this->cacheKey(__FUNCTION__, $slug)) === null) {
            $installed_theme = wp_get_theme($slug);
        }
        if (!$installed_theme || !$installed_theme->exists()) {
            return ''; // Theme not installed.
        } elseif ($parent) { // Looking for parent theme data?
            return $installed_theme->parent() ? (string) $installed_theme->parent()->get($key) : '';
        } else {
            return (string) $installed_theme->get($key);
        }
    }
}
