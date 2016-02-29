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
 * Menu page utils.
 *
 * @since 16xxxx WP notices.
 */
class MenuPage extends Classes\SCore\Base\Core
{
    /**
     * Current menu page.
     *
     * @since 16xxxx First documented version.
     *
     * @return string Current menu page.
     */
    public function current(): string
    {
        if (!is_admin()) {
            return '';
        }
        return !empty($_REQUEST['page'])
            ? $this->c::mbTrim($this->c::unslash((string) $_REQUEST['page']))
            : $this->now(); // `$GLOBALS['pagenow']`.
    }

    /**
     * Current `$GLOBALS['pagenow']`.
     *
     * @since 16xxxx First documented version.
     *
     * @return string Current `$GLOBALS['pagenow']`.
     */
    public function now(): string
    {
        if (!is_admin()) {
            return '';
        }
        return !empty($GLOBALS['pagenow']) ? (string) $GLOBALS['pagenow'] : '';
    }

    /**
     * Is a menu page?
     *
     * @since 16xxxx First documented version.
     *
     * @param string $page Page to check (optional).
     *
     *    - `*` = Zero or more chars.
     *    - `**` = Zero or more chars != `-`.
     *    - Check is caSe insensitive by default.
     *    - If `$page` beings with `/` it is treated as regex.
     *
     * @return bool True if a menu page.
     */
    public function is(string $page = ''): bool
    {
        if (!($current = $this->current())) {
            return false; // Nope.
        }
        if (!$page) {
            return true; // Simple check.
        }
        if ($page[0] === '/') {
            $regex = $page; // Treat as regex.
        } else {
            $regex = '/^'.$this->c::wdRegexFrag($page, '-').'$/ui';
        }
        return (bool) preg_match($regex, $current);
    }

    // @TODO New utils for building menu page components.
}
