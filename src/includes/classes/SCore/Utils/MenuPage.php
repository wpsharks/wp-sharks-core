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
 * Menu page utils.
 *
 * @since 160524 Menu page utils.
 */
class MenuPage extends Classes\SCore\Base\Core
{
    /**
     * In admin area?
     *
     * @since 160524 Initial release.
     *
     * @type bool In admin area?
     */
    protected $is_admin;

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

        $this->is_admin = is_admin();
    }

    /**
     * Current menu page.
     *
     * @since 160524 Menu page utils.
     *
     * @return string Current menu page.
     */
    public function current(): string
    {
        if (!$this->is_admin) {
            return ''; // Not applicable.
        }
        return !empty($_GET['page'])
            ? $this->c::unslash((string) $_GET['page'])
            : $this->now(); // Fallback on `$pagenow`.
    }

    /**
     * Current `$GLOBALS['pagenow']`.
     *
     * @since 160524 Menu page utils.
     *
     * @return string Current `$GLOBALS['pagenow']`.
     */
    public function now(): string
    {
        if (!$this->is_admin) {
            return ''; // Not applicable.
        }
        return (string) ($GLOBALS['pagenow'] ?? '');
    }

    /**
     * Is a menu page?
     *
     * @since 160524 Menu page utils.
     *
     * @param string $page Page to check (optional).
     *
     *    - `*` = Zero or more chars != `-`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
     *    - If `$page` begins with `/` it is treated as regex.
     *
     * @return bool True if a menu page.
     */
    public function is(string $page = ''): bool
    {
        if (!$this->is_admin) {
            return false; // Not applicable.
        } elseif (!($current = $this->current())) {
            return false; // Nope.
        }
        if (!$page) {
            return true; // Simple check.
        }
        if ($page[0] === '/') {
            $regex = $page; // Treat as regex.
        } else {
            $regex = '/^'.$this->c::wregxFrag($page, '-').'$/ui';
        }
        return (bool) preg_match($regex, $current);
    }

    /**
     * Is own menu page?
     *
     * @since 160606 Menu page utils.
     *
     * @param string $page Page to check (optional).
     *
     *    - `*` = Zero or more chars != `-`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
     *    - If `$page` begins with `/` it is treated as regex.
     *
     * @return bool True if own menu page.
     */
    public function isOwn(string $page = ''): bool
    {
        if (!$this->is_admin) {
            return false; // Not applicable.
        }
        $page = $page ?: '{-**,}'; // Any sub-page (or base).
        return $this->is($this->App->Config->©brand['©slug'].$page);
    }

    /**
     * Current menu page tab.
     *
     * @since 160606 Menu page utils.
     *
     * @return string Current menu page post type.
     */
    public function currentTab(): string
    {
        if (!$this->is_admin) {
            return ''; // Not applicable.
        }
        return !empty($_GET['tab'])
            ? $this->c::unslash((string) $_GET['tab'])
            : ''; // Not applicable (no fallback).
    }

    /**
     * Is a menu page tab?
     *
     * @since 160606 Menu page utils.
     *
     * @param string $tab Tab to check (optional).
     *
     *    - `*` = Zero or more chars != `-`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
     *    - If `$tab` begins with `/` it is treated as regex.
     *
     * @return bool True if a menu page tab.
     */
    public function isTab(string $tab = ''): bool
    {
        if (!$this->is_admin) {
            return false; // Not applicable.
        } elseif (!($current = $this->current())) {
            return false; // Nope.
        } elseif (!($current_tab = $this->currentTab())) {
            return false; // Nope.
        }
        if (!$tab) {
            return true; // Simple check.
        }
        if ($tab[0] === '/') {
            $regex = $tab; // Treat as regex.
        } else {
            $regex = '/^'.$this->c::wregxFrag($tab, '-').'$/ui';
        }
        return (bool) preg_match($regex, $current_tab);
    }

    /**
     * Is own menu page tab?
     *
     * @since 160606 Menu page utils.
     *
     * @param string $tab Tab to check (optional).
     *
     *    - `*` = Zero or more chars != `-`.
     *    - `**` = Zero or more chars of any kind.
     *    - Check is always caSe insensitive by default.
     *    - If `$page` begins with `/` it is treated as regex.
     *
     * @return bool True if own menu page tab.
     */
    public function isOwnTab(string $tab = ''): bool
    {
        if (!$this->is_admin) {
            return false; // Not applicable.
        }
        $tab = $tab ?: '{-**,}'; // Any sub-tab (or base).
        return $this->isTab($this->App->Config->©brand['©slug'].$tab);
    }

    /**
     * Current menu page post type.
     *
     * @since 160524 Menu page utils.
     *
     * @return string Current menu page post type.
     */
    public function currentPostType(): string
    {
        if (!$this->is_admin) {
            return ''; // Not applicable.
        }
        return !empty($_GET['post_type'])
            ? $this->c::unslash((string) $_GET['post_type'])
            : $this->postTypeNow(); // Fallback on `$typenow`.
    }

    /**
     * Current `$GLOBALS['typenow']`.
     *
     * @since 160524 Menu page utils.
     *
     * @return string Current `$GLOBALS['typenow']`.
     */
    public function postTypeNow(): string
    {
        if (!$this->is_admin) {
            return ''; // Not applicable.
        }
        return (string) ($GLOBALS['typenow'] ?? '');
    }

    /**
     * Is a menu page for a post type?
     *
     * @since 160524 Menu page utils.
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
        if (!$this->is_admin) {
            return false; // Not applicable.
        } elseif (!($current = $this->current())) {
            return false; // Nope.
        } elseif (!($current_post_type = $this->currentPostType())) {
            return false; // Nope.
        } elseif (!in_array($current, ['post-new.php', 'post.php', 'edit.php', 'edit-tags.php'], true)) {
            return false; // Nope.
        }
        if (!$post_type) {
            return true; // Simple check.
        }
        if ($post_type[0] === '/') {
            $regex = $post_type; // Treat as regex.
        } else {
            $regex = '/^'.$this->c::wregxFrag($post_type, '_').'$/ui';
        }
        return (bool) preg_match($regex, $current_post_type);
    }
}
