<?php
/**
 * Menu page utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
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
     * Menu page hook names.
     *
     * @since 160715 Conflicts.
     *
     * @var array Menu page hook names.
     */
    protected $hook_names;

    /**
     * Class constructor.
     *
     * @since 160715 Conflicts.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->hook_names = [];
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
     * Filter action links.
     *
     * @since 160715 Menu page utils.
     *
     * @param array $actions Current action links.
     *
     * @return array Filtered action links.
     */
    public function onPluginActionLinks(array $actions): array
    {
        if ($this->App->Config->§specs['§type'] !== 'plugin') {
            return $actions; // Not applicable.
        }
        if (($default_url = $this->defaultUrl())) { // See {@link defaultUrl()} below.
            $actions[] = '<a href="'.esc_url($default_url).'">'.__('Settings', 'wp-sharks-core').'</a>';
        }
        if (!$this->App->Config->§specs['§is_pro'] && $this->App->Config->§specs['§has_pro']) {
            $actions[] = '<a href="'.esc_url($this->s::brandUrl('/', true)).'" target="_blank">'.__('Upgrade', 'wp-sharks-core').' <i class="sharkicon sharkicon-octi-tag"></i></a>';
        } elseif ($this->App->is_core) {
            $actions[] = '<a href="'.esc_url($this->s::coreUrl('/shop')).'" target="_blank">'.esc_html($this->App::CORE_CONTAINER_NAME).'™ <i class="sharkicon sharkicon-wp-sharks-fin"></i></a>';
        }
        return $actions;
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
            'auto_prefix'   => true,
            'page_title'    => '',
            'menu_title'    => '',
            'capability'    => '',
            'page'          => '',
            'class'         => '',
            'template_file' => '',
            'template_dir'  => '',
            'icon'          => '',
            'position'      => null,
            'meta_links'    => [],
            'tabs'          => [],
            'callback'      => null,
        ];
        $cfg = (object) array_merge($default_args, $args);

        $cfg->auto_prefix   = (bool) $cfg->auto_prefix;
        $cfg->page_title    = (string) $cfg->page_title;
        $cfg->menu_title    = (string) $cfg->menu_title;
        $cfg->capability    = (string) $cfg->capability;
        $cfg->page          = (string) $cfg->page;
        $cfg->class         = (string) $cfg->class;
        $cfg->template_file = (string) $cfg->template_file;
        $cfg->template_dir  = (string) $cfg->template_dir;
        $cfg->icon          = (string) $cfg->icon;
        $cfg->position      = (string) $cfg->position;
        $cfg->meta_links    = (array) $cfg->meta_links;
        $cfg->tabs          = (array) $cfg->tabs;

        if ($cfg->page_title && $cfg->auto_prefix // Smart auto-prefixing.
                && $cfg->page_title !== $this->App->Config->©brand['©name']) {
            $cfg->page_title = $cfg->page_title.' | '.$this->App->Config->©brand['©name'];
        } elseif (!$cfg->page_title) {
            $cfg->page_title = $this->App->Config->©brand['©name'];
        }
        if (!$cfg->menu_title) {
            $cfg->menu_title = $this->App->Config->©brand['©name'];
        }
        if (!$cfg->capability) {
            $cfg->capability = $this->App->Config->§caps['§manage'];
        }
        if ($cfg->page && $cfg->auto_prefix // Smart auto-prefixing.
                && $cfg->page[0] !== '/' && mb_stripos($cfg->page, '.php') === false) {
            $cfg->page = $this->App->Config->©brand['©slug'].'-'.$cfg->page;
        } elseif (!$cfg->page) {
            $cfg->page = $this->App->Config->©brand['©slug'];
        }
        $cfg->class .= ($cfg->class ? ' ' : '').'wrap';
        $cfg->class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $cfg->class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-wrapper';
        $cfg->class .= ' '.$this->App->Config->©brand['©slug'].'-menu-page-wrapper';
        $cfg->class .= $cfg->page !== $this->App->Config->©brand['©slug'] ? ' '.$cfg->page.'-menu-page-wrapper' : '';

        if (!$cfg->icon) {
            $cfg->icon = 'dashicons-admin-generic';
        }
        if (!isset($cfg->position[0])) {
            $cfg->position = null; // No preference.
        }
        $cfg = $this->s::applyFilters('menu_page', $cfg, $args, $default_args);

        if (!$cfg->template_file) {
            throw $this->c::issue('Missing template file.');
        }
        $cfg->nav_tabs = $this->is($cfg->page) ? $this->buildNavTabs($cfg) : '';
        $cfg->callback = $cfg->callback ?: function () use ($cfg) {
            echo $this->c::getTemplate('s-core/admin/menu-pages/template.php')->parse(compact('cfg'));
        };
        $this->hook_names[$cfg->page] = // By slug. See: <https://developer.wordpress.org/reference/functions/add_menu_page/>
            add_menu_page($cfg->page_title, $cfg->menu_title, $cfg->capability, $cfg->page, $cfg->callback, $cfg->icon, $cfg->position);
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
            'parent_page'   => '',
            'page_title'    => '',
            'menu_title'    => '',
            'capability'    => '',
            'page'          => '',
            'class'         => '',
            'template_file' => '',
            'template_dir'  => '',
            'meta_links'    => [],
            'tabs'          => [],
            'callback'      => null,
        ];
        $cfg = (object) array_merge($default_args, $args);

        $cfg->auto_prefix   = (bool) $cfg->auto_prefix;
        $cfg->parent_page   = (string) $cfg->parent_page;
        $cfg->page_title    = (string) $cfg->page_title;
        $cfg->menu_title    = (string) $cfg->menu_title;
        $cfg->capability    = (string) $cfg->capability;
        $cfg->page          = (string) $cfg->page;
        $cfg->class         = (string) $cfg->class;
        $cfg->template_file = (string) $cfg->template_file;
        $cfg->template_dir  = (string) $cfg->template_dir;
        $cfg->meta_links    = (array) $cfg->meta_links;
        $cfg->tabs          = (array) $cfg->tabs;

        if ($cfg->parent_page && $cfg->auto_prefix // Smart auto-prefixing.
                && $cfg->parent_page[0] !== '/' && mb_stripos($cfg->parent_page, '.php') === false) {
            $cfg->parent_page = $this->App->Config->©brand['©slug'].'-'.$cfg->parent_page;
        } elseif (!$cfg->parent_page) {
            $cfg->parent_page = $this->App->Config->©brand['©slug'];
        }
        if ($cfg->page_title && $cfg->auto_prefix // Smart auto-prefixing.
                && $cfg->page_title !== $this->App->Config->©brand['©name']) {
            $cfg->page_title = $cfg->page_title.' | '.$this->App->Config->©brand['©name'];
        } elseif (!$cfg->page_title) {
            $cfg->page_title = $this->App->Config->©brand['©name'];
        }
        if (!$cfg->menu_title) {
            $cfg->menu_title = $this->App->Config->©brand['©name'];
        }
        if (!$cfg->capability) {
            $cfg->capability = $this->App->Config->§caps['§manage'];
        }
        if ($cfg->page && $cfg->auto_prefix // Smart auto-prefixing.
                && $cfg->page[0] !== '/' && mb_stripos($cfg->page, '.php') === false) {
            $cfg->page = $this->App->Config->©brand['©slug'].'-'.$cfg->page;
        } elseif (!$cfg->page) {
            $cfg->page = $this->App->Config->©brand['©slug'];
        }
        $cfg->class .= ($cfg->class ? ' ' : '').'wrap';
        $cfg->class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $cfg->class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-menu-page-wrapper';
        $cfg->class .= ' '.$this->App->Config->©brand['©slug'].'-menu-page-wrapper';
        $cfg->class .= $cfg->page !== $this->App->Config->©brand['©slug'] ? ' '.$cfg->page.'-menu-page-wrapper' : '';

        $cfg = $this->s::applyFilters('menu_page_item', $cfg, $args, $default_args);

        if (!$cfg->template_file) {
            throw $this->c::issue('Missing template file.');
        }
        $cfg->nav_tabs = $this->is($cfg->page) ? $this->buildNavTabs($cfg) : '';
        $cfg->callback = $cfg->callback ?: function () use ($cfg) {
            echo $this->c::getTemplate('s-core/admin/menu-pages/template.php')->parse(compact('cfg'));
        };
        $this->hook_names[$cfg->parent_page.':'.$cfg->page] = // By slug. See: <https://developer.wordpress.org/reference/functions/add_submenu_page/>
            add_submenu_page($cfg->parent_page, $cfg->page_title, $cfg->menu_title, $cfg->capability, $cfg->page, $cfg->callback);
    }

    /**
     * Builds navigation tabs.
     *
     * @since 160708 Menu page utils.
     *
     * @param \StdClass $cfg Menu page config.
     *
     * @return string Markup for navigation tabs.
     */
    protected function buildNavTabs(\StdClass $cfg): string
    {
        if (!$cfg->tabs) {
            return ''; // Nothing.
        } elseif (!$this->is($cfg->page)) {
            return ''; // Not necessary.
        }
        $has_default_tab  = false;
        $current_tab      = $this->currentTab();
        $current_page_url = $this->c::currentUrl();
        $current_page_url = $this->s::removeUrlNonce($current_page_url);
        $current_page_url = $this->s::removeUrlRestAction($current_page_url);
        $current_page_url = $this->c::removeUrlQueryArgs(['tab'], $current_page_url);

        $markup = '<nav class="-nav-tabs nav-tab-wrapper">';

        foreach ($cfg->tabs as $_key => &$_tab) {
            if (!$_key || !is_string($_key)) {
                throw $this->c::issue('Invalid key.');
            }
            if ($_key === 'restore') {
                $cfg->meta_links['restore'] = true;
                continue; // Back compat for restore.
            }
            if ($_tab && is_string($_tab)) {
                $_tab = ['label' => $_tab];
            }
            $_tab = array_merge([
                'slug'          => '',
                'url'           => '',
                'target'        => '',
                'onclick'       => '',
                'class'         => '',
                'label'         => '',
                'template_file' => '',
                'template_dir'  => '',
            ], (array) $_tab); // Force array.

            $_tab['slug'] = $this->c::nameToSlug($_key);

            if (!$_tab['url'] || $_tab['slug'] === 'default') {
                if ($_tab['slug'] === 'default') {
                    $_tab['url'] = $current_page_url;
                } else { // Add `&tab=` to the current page URL.
                    $_tab['url'] = $this->c::addUrlQueryArgs(['tab' => $_tab['slug']], $current_page_url);
                }
            } // ↑ This constructs the URL leading to a given tab.

            if ($_tab['onclick'] === 'confirm') { // Automatic confirmation.
                $_tab['onclick'] = 'if(!confirm(\''.__('Are you sure?', 'wp-sharks-core').'\')) return false;';
            }
            $_tab['class'] .= ($_tab['class'] ? ' ' : '').'-nav-tab nav-tab';
            $_tab['class'] .= ' -'.$_tab['slug']; // Tab-specific class.

            if ($_tab['slug'] === $current_tab || ($_tab['slug'] === 'default' && !$current_tab)) {
                $_tab['class'] .= ' -active nav-tab-active';

                // NOTE: This allows a tab to set it's own template.
                // Helpful to extensions that filter menu page config values.
                // This is made possible 'by reference'.

                if ($_tab['template_file']) {
                    // Swap template if set by tab config.
                    $cfg->template_file = $_tab['template_file'];
                    $cfg->template_dir  = $_tab['template_dir'];
                    //
                } elseif ($current_tab) { // Use default swap behavior; i.e., add tab suffix.
                    // NOTE: In the case of there being no `$current_tab`, leaving default template as-is.
                    $cfg->template_file = dirname($cfg->template_file).'/'.$current_tab.'.php';
                }
            }
            if (!$_tab['label']) {
                $_tab['label'] = esc_html($this->c::slugToName($_tab['slug']));
            }
            $markup .= '<a'.// Builds tab.
                       ' href="'.esc_url($_tab['url']).'"'.
                       ' target="'.esc_attr($_tab['target']).'"'.
                       ' onclick="'.esc_attr($_tab['onclick']).'"'.
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
        } // ↑ Tab as 2nd parameter if it's a string.

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

        if (!$page_path || $page_path[0] === '/' || mb_stripos($page_path, '.php') !== false) {
            $path       = '/'.$this->c::mbLTrim($page_path, '/');
            return $url = $this->c::addUrlQueryArgs($query_args, $admin_url($path));
            //
        } else { // Treat as `?page` instead of a path.
            $page       = $this->c::mbTrim($page_path, '/');
            $query_args = array_merge(['page' => $page], $query_args);
            return $url = $this->c::addUrlQueryArgs($query_args, $admin_url('/admin.php'));
        }
    }

    /**
     * Default menu page URL.
     *
     * @since 161026 Menu page utils.
     *
     * @return string Default menu page URL.
     */
    public function defaultUrl(): string
    {
        if (!empty($this->hook_names[$this->App->Config->©brand['©slug']])) {
            return $this->url(); // Nothing special in this case.
            //
        } else { // Search for sub-menu item matching slug regex pattern.
            $_page_regex = $this->c::escRegex($this->App->Config->©brand['©slug']);

            foreach ($this->hook_names as $_hook_page_key => $_hook_name) { // Find parent page.
                if (preg_match('/^(?<parent_page>[^:]+)\:(?<page>'.$_page_regex.')$/u', $_hook_page_key, $_m)) {
                    return $this->url($_m['parent_page'], ['page' => $_m['page']]);
                }
            } // unset($_page_regex, $_hook_page_key, $_hook_name); // Houskeeping.
        }
        return ''; // Not possible.
    }

    /**
     * A menu page form class instance.
     *
     * @since 160524 Menu page markup utils.
     *
     * @param string $action ReST action identifier.
     * @param array  $args   Any additional behavioral args.
     *
     * @return Classes\SCore\MenuPageForm Class instance.
     */
    public function form(string $action, array $args = []): Classes\SCore\MenuPageForm
    {
        return $this->App->Di->get(Classes\SCore\MenuPageForm::class, compact('action', 'args'));
    }
}
