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
     * Current menu page.
     *
     * @since 160524 Menu page utils.
     *
     * @return string Current menu page.
     */
    public function current(): string
    {
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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
        if (!$this->Wp->is_admin) {
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

    /**
     * Admin body class filter.
     *
     * @since 160708 Menu page utils.
     *
     * @param scalar $class Current body class.
     * @param string Filtered body class.
     */
    public function onAdminBodyClass($class): string
    {
        $class = (string) $class;

        if (!$this->isOwn()) {
            return $class; // Not applicable.
        }
        $tab = $this->currentTab(); // Request var.
        $tab = !$tab || !$this->c::isSlug($tab) ? '' : $tab;

        $class .= ($class ? ' ' : '').$this->App::CORE_CONTAINER_SLUG.'-menu-page';
        $class .= ' '.$this->App->Config->©brand['©slug'].'-menu-page'; // App identifiers.
        $class .= $tab ? ' '.$this->App->Config->©brand['©slug'].'-menu-page-tab-'.$tab : '';

        return $class;
    }

    /**
     * Adds a new top-level menu.
     *
     * @since 160708 Menu page utils.
     *
     * @param array $args Configuration args.
     */
    public function addMenu(array $args = [])
    {
        $default_args = [
            'page_title'    => '',
            'menu_title'    => '',
            'capability'    => '',
            'slug'          => '',
            'class'         => '',
            'template_file' => '',
            'template_dir'  => '',
            'icon'          => '',
            'position'      => null,
            'tabs'          => [],
            'callback'      => null,
        ];
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);

        $cfg['page_title']    = (string) $cfg['page_title'];
        $cfg['menu_title']    = (string) $cfg['menu_title'];
        $cfg['capability']    = (string) $cfg['capability'];
        $cfg['slug']          = (string) $cfg['slug'];
        $cfg['class']         = (string) $cfg['class'];
        $cfg['template_file'] = (string) $cfg['template_file'];
        $cfg['template_dir']  = (string) $cfg['template_dir'];
        $cfg['icon']          = (string) $cfg['icon'];
        $cfg['position']      = (string) $cfg['position'];
        $cfg['tabs']          = (array) $cfg['tabs'];

        if (!$cfg['page_title']) {
            $cfg['page_title'] = $this->App->Config->©brand['©name'];
        }
        if (!$cfg['menu_title']) {
            $cfg['menu_title'] = $this->App->Config->©brand['©name'];
        }
        if (!$cfg['capability']) {
            $cfg['capability'] = $this->App->Config->§caps['§manage'];
        }
        if (!$cfg['slug']) {
            $cfg['slug'] = $this->App->Config->©brand['©slug'];
        }
        $cfg['class'] .= ($cfg['class'] ? ' ' : '').'wrap';
        $cfg['class'] .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $cfg['class'] .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-wrapper';
        $cfg['class'] .= ' '.$this->App->Config->©brand['©slug'].'-menu-page-wrapper';
        $cfg['class'] .= $cfg['slug'] !== $this->App->Config->©brand['©slug'] ? ' '.$cfg['slug'].'-menu-page-wrapper' : '';

        if (!$cfg['template_file']) {
            throw $this->c::issue('Missing template file.');
        }
        if (!$cfg['icon']) {
            $cfg['icon'] = 'dashicons-admin-generic';
        }
        if (!isset($cfg['position'][0])) {
            $cfg['position'] = null; // No preference.
        }
        $cfg['nav_tabs'] = $this->buildNavTabs($cfg);
        $cfg['callback'] = $cfg['callback'] ?: function () use (&$cfg) {
            echo $this->c::getTemplate('s-core/menu-pages/template.php')->parse(compact('cfg'));
        };
        add_menu_page($cfg['page_title'], $cfg['menu_title'], $cfg['capability'], $cfg['slug'], $cfg['callback'], $cfg['icon'], $cfg['position']);
    }

    /**
     * Adds a new menu item.
     *
     * @since 160708 Menu page utils.
     *
     * @param array $args Configuration args.
     */
    public function addMenuItem(array $args = [])
    {
        $default_args = [
            'auto_prefix'   => true,
            'parent_slug'   => '',
            'page_title'    => '',
            'menu_title'    => '',
            'capability'    => '',
            'slug'          => '',
            'class'         => '',
            'template_file' => '',
            'template_dir'  => '',
            'tabs'          => [],
            'callback'      => null,
        ];
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);

        $cfg['auto_prefix']   = (bool) $cfg['auto_prefix'];
        $cfg['parent_slug']   = (string) $cfg['parent_slug'];
        $cfg['page_title']    = (string) $cfg['page_title'];
        $cfg['menu_title']    = (string) $cfg['menu_title'];
        $cfg['capability']    = (string) $cfg['capability'];
        $cfg['slug']          = (string) $cfg['slug'];
        $cfg['class']         = (string) $cfg['class'];
        $cfg['template_file'] = (string) $cfg['template_file'];
        $cfg['template_dir']  = (string) $cfg['template_dir'];
        $cfg['tabs']          = (array) $cfg['tabs'];

        if ($cfg['parent_slug'] && $cfg['auto_prefix']) {
            $cfg['parent_slug'] = $this->App->Config->©brand['©slug'].'-'.$cfg['parent_slug'];
        } elseif (!$cfg['parent_slug']) {
            $cfg['parent_slug'] = $this->App->Config->©brand['©slug'];
        }
        if ($cfg['page_title'] && $cfg['auto_prefix']) {
            $cfg['page_title'] = $cfg['page_title'].' | '.$this->App->Config->©brand['©name'];
        } elseif (!$cfg['page_title']) {
            $cfg['page_title'] = $this->App->Config->©brand['©name'];
        }
        if (!$cfg['menu_title']) {
            $cfg['menu_title'] = $this->App->Config->©brand['©name'];
        }
        if (!$cfg['capability']) {
            $cfg['capability'] = $this->App->Config->§caps['§manage'];
        }
        if ($cfg['slug'] && $cfg['auto_prefix']) {
            $cfg['slug'] = $this->App->Config->©brand['©slug'].'-'.$cfg['slug'];
        } elseif (!$cfg['slug']) {
            $cfg['slug'] = $this->App->Config->©brand['©slug'];
        }
        $cfg['class'] .= ($cfg['class'] ? ' ' : '').'wrap';
        $cfg['class'] .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $cfg['class'] .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-wrapper';
        $cfg['class'] .= ' '.$this->App->Config->©brand['©slug'].'-menu-page-wrapper';
        $cfg['class'] .= $cfg['slug'] !== $this->App->Config->©brand['©slug'] ? ' '.$cfg['slug'].'-menu-page-wrapper' : '';

        if (!$cfg['template_file']) {
            throw $this->c::issue('Missing template file.');
        }
        $cfg['nav_tabs'] = $this->buildNavTabs($cfg);
        $cfg['callback'] = $cfg['callback'] ?: function () use (&$cfg) {
            echo $this->c::getTemplate('s-core/menu-pages/template.php')->parse(compact('cfg'));
        };
        add_submenu_page($cfg['parent_slug'], $cfg['page_title'], $cfg['menu_title'], $cfg['capability'], $cfg['slug'], $cfg['callback']);
    }

    /**
     * Builds navigation tabs.
     *
     * @since 160708 Menu page utils.
     *
     * @param array &$cfg Menu page config.
     *
     * @return string Markup for navigation tabs.
     */
    protected function buildNavTabs(array &$cfg): string
    {
        if (!$cfg['tabs']) {
            return ''; // N/A.
        }
        $has_default_tab   = false; // Initialize.
        $is_this_menu_page = $this->is($cfg['slug']);
        $current_tab       = $this->currentTab();

        $markup = '<nav class="-nav-tabs nav-tab-wrapper">';

        foreach ($cfg['tabs'] as $_key => &$_tab) {
            if (!$_key || !is_string($_key)) {
                throw $this->c::issue('Invalid key.');
            }
            if ($_tab && is_string($_tab)) {
                $_tab = ['label' => $_tab];
            }
            $_tab = array_merge([
                'slug'   => '',
                'url'    => '',
                'target' => '',
                'class'  => '',
                'label'  => '',
            ], (array) $_tab); // Force array.

            $_tab['slug'] = $this->c::nameToSlug($_key);

            if (!$_tab['url'] || $_tab['slug'] === 'default') {
                if ($_tab['slug'] === 'default') {
                    $_tab['url'] = $this->c::currentUrl();
                    $_tab['url'] = $this->c::removeUrlQueryArgs(['tab'], $_tab['url']);
                } else {
                    $_tab['url'] = $this->c::currentUrl();
                    $_tab['url'] = $this->c::addUrlQueryArgs(['tab' => $_tab['slug']], $_tab['url']);
                }
                $_tab['url'] = $this->s::removeUrlRestAction($_tab['url']);
                $_tab['url'] = $this->s::removeUrlNonce($_tab['url']);
            }
            $_tab['class'] .= ($_tab['class'] ? ' ' : '').'-nav-tab nav-tab';

            if ($_tab['slug'] === 'default') {
                if ($is_this_menu_page && !$current_tab) {
                    $_tab['class'] .= ' -active nav-tab-active';
                }
            } elseif ($is_this_menu_page && $current_tab === $_tab['slug']) {
                $_tab['class'] .= ' -active nav-tab-active';
                $cfg['template_file'] = dirname($cfg['template_file']).'/'.$current_tab.'.php';
            }
            $_tab['class'] .= ' -'.$_tab['slug']; // Tab-specific class.

            if (!$_tab['label']) {
                $_tab['label'] = esc_html($this->c::slugToName($_tab['slug']));
            }
            $markup .= '<a'.// Builds tab.
                       ' href="'.esc_url($_tab['url']).'"'.
                       ' target="'.esc_attr($_tab['target']).'"'.
                       ' class="'.esc_attr($_tab['class']).'"'.
                       ' >'.$_tab['label'].'</a>';

            if ($_tab['slug'] === 'default') {
                $has_default_tab = true;
            }
        }
        if (!$has_default_tab) {
            throw $this->c::issue('Missing `default` tab.');
        }
        return $markup .= '</nav>';
    }

    /**
     * Menu page URL.
     *
     * @since 160712 Menu page utils.
     *
     * @param string       $page_path  Page (or path).
     * @param array|string $query_args Query args (or tab).
     * @param string       $context    `admin`, `network`, `self`.
     *
     * @return string Current menu page.
     */
    public function url(string $page_path = '', $query_args = [], string $context = ''): string
    {
        switch ($context) {
            case 'admin':
                $admin_url = 'admin_url';
                break;

            case 'network':
                $admin_url = 'network_admin_url';
                break;

            case 'self':
            default: // Default case handler.
                $admin_url = 'self_admin_url';
                break;
        }
        if (!is_array($query_args)) {
            if ($query_args && is_string($query_args)) {
                $query_args = ['tab' => $query_args];
            } else {
                $query_args = []; // Force array.
            }
        }
        $_tmp_query_args = $query_args;
        $query_args      = []; // Reinitialize.

        $brand_slug       = $this->App->Config->©brand['©slug'];
        $brand_short_slug = $this->App->Config->©brand['©short_slug'];

        $brand_var       = $this->App->Config->©brand['©var'];
        $brand_short_var = $this->App->Config->©brand['©short_var'];

        foreach ($_tmp_query_args as $_key => $_value) {
            if ($_key && is_string($_key)) {
                $_key = str_replace('~', $brand_var, $_key);
                $_key = str_replace('%', $brand_short_var, $_key);
            }
            if ($_value && is_string($_value)) {
                $_value = str_replace('~', $brand_slug, $_value);
                $_value = str_replace('%', $brand_short_slug, $_value);
            }
            $query_args[$_key] = $_value; // New `key` => `value`.
        } // unset($_tmp_query_args, $_key, $_value); // Housekeeping.

        $page_path = $page_path ?: $brand_slug;
        $page_path = str_replace('~', $brand_slug, $page_path);
        $page_path = str_replace('%', $brand_short_slug, $page_path);

        if (!$page_path || mb_strpos($page_path, '/') !== false || mb_substr($page_path, -4) === '.php') {
            $path       = '/'.$this->c::mbLTrim($page_path, '/');
            $page       = ''; // No `page`; treating this as a `/path`.
            return $url = $this->c::addUrlQueryArgs($query_args, $admin_url($path));
            //
        } else { // Treat it as a `?page` instead of a path.
            $page       = $page_path;
            $path       = ''; // No `path`; treating as a `?page`.
            $query_args = array_merge(['page' => $page], $query_args);
            return $url = $this->c::addUrlQueryArgs($query_args, $admin_url('/'));
        }
    }
}
