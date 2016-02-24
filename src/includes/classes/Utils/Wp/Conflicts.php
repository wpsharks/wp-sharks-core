<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin conflicts.
 *
 * @since 16xxxx WP notices.
 */
class Conflicts extends WCoreClasses\PluginBase
{
    /**
     * Checked?
     *
     * @since 16xxxx
     *
     * @type bool Checked?
     */
    protected $checked = false;

    /**
     * Conflicting plugins.
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $plugins = [];

    /**
     * Conflicting themes.
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $themes = [];

    /**
     * Deactivatble plugins.
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $deactivatble_plugins = [];

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $this->check();
    }

    /**
     * Conflicts exist?
     *
     * @since 16xxxx Initial release.
     *
     * @return bool Conflicts exist?
     */
    public function exist(): bool
    {
        return $this->plugins || $this->themes
            || $this->deactivatble_plugins;
    }

    /**
     * Check for conflicts.
     *
     * @since 16xxxx Initial release.
     *
     * @note Deactivable conflicts are considered conflicts too.
     *  However, we can deactivate them and simply show a warning w/ refresh link.
     */
    public function check()
    {
        if ($this->checked) {
            return; // Done.
        }
        $this->checked = true;

        $Config = $this->Plugin->Config;

        if (!$Config->conflicting['plugins']) {
            return; // Nothing to do here.
        }
        $is_admin          = is_admin();
        $active_theme_slug = get_template();
        $active_plugins    = wc\all_active_plugins();

        $conflicting_plugins      = $Config->conflicting['plugins'];
        $conflicting_plugin_slugs = array_keys($Config->conflicting['plugins']);

        $deactivatble_plugins      = $Config->conflicting['deactivatble_plugins'];
        $deactivatble_plugin_slugs = array_keys($Config->conflicting['deactivatble_plugins']);

        $conflicting_themes      = $Config->conflicting['themes'];
        $conflicting_theme_slugs = array_keys($Config->conflicting['themes']);

        foreach ($active_plugins as $_active_plugin_basename) {
            if (!($_active_plugin_slug = mb_strstr($_active_plugin_basename, '/', true))) {
                continue; // Sanity check. Cannot be empty.
            }
            if ($_active_plugin_slug === $Config->brand['slug']) {
                continue; // Sanity check. Cannot conflict w/ self.
            }
            if (in_array($_active_plugin_slug, $conflicting_plugin_slugs, true)) {
                $_conflicting_plugin_basename = $_active_plugin_basename;
                $_conflicting_plugin_slug     = $_active_plugin_slug;

                if ($is_admin && !defined('WP_UNINSTALL_PLUGIN') && in_array($_conflicting_plugin_slug, $deactivatble_plugin_slugs, true)) {
                    $this->deactivatble_plugins[$_conflicting_plugin_slug] = $conflicting_plugins[$_conflicting_plugin_slug];

                    add_action('admin_init', function () use ($_conflicting_plugin_basename) {
                        deactivate_plugins($_conflicting_plugin_basename, true);
                    }, -10000); // With a very early priority.
                } else {
                    $this->plugins[$_conflicting_plugin_slug] = $conflicting_plugins[$_conflicting_plugin_slug];
                }
            }
        } // unset($_active_plugin_basename, $_active_plugin_slug, $_conflicting_plugin_basename, $_conflicting_plugin_slug);

        if ($active_theme_slug && $conflicting_theme_slugs && in_array($active_theme_slug, $conflicting_theme_slugs, true)) {
            $_conflicting_theme_slug                = $active_theme_slug;
            $this->themes[$_conflicting_theme_slug] = $conflicting_themes[$_conflicting_theme_slug];
        } // unset($_conflicting_theme_slug); // Housekeeping.

        $this->maybeNotify(); // If conflicts exist.
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 16xxxx Rewrite.
     */
    protected function maybeNotify()
    {
        $Config = $this->Plugin->Config;

        if ($this->plugins) {
            add_action('all_admin_notices', function () use ($Config) {
                if (!current_user_can('activate_plugins')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-error">'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> is NOT running yet. A conflicting plugin, <strong>%2$s</strong>, is currently active. Please deactivate the \'%2$s\' plugin to clear this message.'), esc_html($Config->brand['name']), esc_html(end($this->plugins))).'</p>'.
                     '</div>';
            });
        } elseif ($this->themes) {
            add_action('all_admin_notices', function () use ($Config) {
                if (!current_user_can('activate_plugins')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-error">'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> is NOT running yet. A conflicting theme, <strong>%2$s</strong>, is currently active. Please deactivate the \'%2$s\' theme to clear this message.'), esc_html($Config->brand['name']), esc_html(end($this->themes))).'</p>'.
                     '</div>';
            });
        } elseif ($this->deactivatble_plugins) {
            add_action('all_admin_notices', function () use ($Config) {
                if (!current_user_can('activate_plugins')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-warning">'.
                        '<p>'.sprintf(__('The following plugins were deactivated automatically because they conflict with <strong>%1$s</strong>. Deactivated: <em>%2$s</em>; in favor of <strong>%1$s</strong>.'), esc_html($Config->brand['name']), esc_html(implode(', ', $this->deactivatble_plugins))).'</p>'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> will load now. Please <a href="javascript:location.reload();">refresh</a>.'), esc_html($Config->brand['name'])).'</p>'.
                     '</div>';
            });
        }
    }
}
