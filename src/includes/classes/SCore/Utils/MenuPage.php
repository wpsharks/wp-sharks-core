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
     * Current menu page post type.
     *
     * @since 16xxxx First documented version.
     *
     * @return string Current menu page post type.
     */
    public function currentPostType(): string
    {
        if (!is_admin()) {
            return '';
        }
        return !empty($_REQUEST['post_type'])
            ? $this->c::mbTrim($this->c::unslash((string) $_REQUEST['post_type']))
            : $this->postTypeNow(); // `$GLOBALS['typenow']`.
    }

    /**
     * Current `$GLOBALS['typenow']`.
     *
     * @since 16xxxx First documented version.
     *
     * @return string Current `$GLOBALS['typenow']`.
     */
    public function postTypeNow(): string
    {
        if (!is_admin()) {
            return '';
        }
        return !empty($GLOBALS['typenow']) ? (string) $GLOBALS['typenow'] : '';
    }

    /**
     * Is a menu page?
     *
     * @since 16xxxx First documented version.
     *
     * @param string $page Page to check (optional).
     *
     *    - `*` = Zero or more chars != `-`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
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

    /**
     * Is a menu page for a post type?
     *
     * @since 16xxxx First documented version.
     *
     * @param string $post_type Post type to check (optional).
     *
     *    - `*` = Zero or more chars != `_`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
     *    - If `$post_type` beings with `/` it is treated as regex.
     *
     * @return bool True if menu page is for post type.
     */
    public function isForPostType(string $post_type = ''): bool
    {
        if (!($current = $this->current())) {
            return false; // Nope.
        }
        if (!($current_post_type = $this->currentPostType())) {
            return false; // Nope.
        }
        if (!in_array($current, ['post-new.php', 'post.php', 'edit.php', 'edit-tags.php'], true)) {
            return false; // Nope.
        }
        if (!$post_type) {
            return true; // Simple check.
        }
        if ($post_type[0] === '/') {
            $regex = $post_type; // Treat as regex.
        } else {
            $regex = '/^'.$this->c::wdRegexFrag($post_type, '_').'$/ui';
        }
        return (bool) preg_match($regex, $current_post_type);
    }
}
