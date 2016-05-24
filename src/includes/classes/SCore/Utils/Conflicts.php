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
 * Conflicts.
 *
 * @since 160525 WP notices.
 */
class Conflicts extends Classes\SCore\Base\Core
{
    /**
     * Conflicting plugins.
     *
     * @since 160525
     *
     * @type array Slugs.
     */
    protected $plugins;

    /**
     * Conflicting themes.
     *
     * @since 160525
     *
     * @type array Slugs.
     */
    protected $themes;

    /**
     * Deactivatble plugins.
     *
     * @since 160525
     *
     * @type array Slugs.
     */
    protected $deactivatable_plugins;

    /**
     * Checked?
     *
     * @since 160525
     *
     * @type bool Checked?
     */
    protected $checked;

    /**
     * Max OK time age.
     *
     * @since 160525
     *
     * @type int Max age.
     */
    protected $max_ok_age;

    /**
     * Class constructor.
     *
     * @since 160525 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->plugins               = [];
        $this->themes                = [];
        $this->deactivatable_plugins = [];
        $this->checked               = false;
        $this->max_ok_age            = strtotime('-15 minutes');

        $this->maybeCheck(); // On instantiation.
    }

    /**
     * Conflicts exist?
     *
     * @since 160525 Initial release.
     *
     * @return bool Conflicts exist?
     *
     * @note Deactivable conflicts are considered conflicts too.
     *  However, we can deactivate them and simply show a warning w/ refresh link.
     */
    public function exist(): bool
    {
        return $this->plugins || $this->themes || $this->deactivatable_plugins;
    }

    /**
     * Check for conflicts.
     *
     * @since 160525 Initial release.
     *
     * @note Deactivable conflicts are considered conflicts too.
     *  However, we can deactivate them and simply show a warning w/ refresh link.
     */
    protected function maybeCheck()
    {
        if ($this->checked) {
            return; // Done.
        }
        $this->checked = true; // Checking now.

        if (!$this->App->Config->§conflicts['§plugins']
            && !$this->App->Config->§conflicts['§themes']) {
            return; // Nothing to do here.
        } elseif (($is_front_or_ajax = $this->s::isFrontOrAjax())
                && $this->lastOkTime() >= $this->max_ok_age) {
            return; // Had a successfull check recently.
        }
        $is_admin     = is_admin();
        $is_multisite = is_multisite();

        $all_active_plugin_slugs     = $this->s::allActivePlugins();
        $network_active_plugin_slugs = $is_multisite ? $this->s::networkActivePlugins() : [];
        $all_active_theme_slugs      = array_unique([get_template(), get_stylesheet()]);

        $conflicting_plugin_slugs   = array_keys($this->App->Config->§conflicts['§plugins']);
        $conflicting_theme_slugs    = array_keys($this->App->Config->§conflicts['§themes']);
        $deactivatable_plugin_slugs = array_keys($this->App->Config->§conflicts['§deactivatable_plugins']);

        foreach ($all_active_plugin_slugs as $_active_plugin_basename => $_active_plugin_slug) {
            if ($_active_plugin_slug === $this->App->base_dir_basename && $this->App->Config->§specs['§type'] === 'plugin') {
                continue; // Sanity check. Cannot depend on self of the same type.
            } elseif (!in_array($_active_plugin_slug, $conflicting_plugin_slugs, true)) {
                continue; // Not a conflicting plugin.
            }
            $_conflicting_plugin_basename = &$_active_plugin_basename;
            $_conflicting_plugin_slug     = &$_active_plugin_slug;

            if ($is_admin && !defined('WP_UNINSTALL_PLUGIN') && in_array($_conflicting_plugin_slug, $deactivatable_plugin_slugs, true)
                && (!$is_multisite || !in_array($_conflicting_plugin_slug, $network_active_plugin_slugs, true))) {
                // ↑ Note that we do not deactivate network-wide plugins, as that could impact other sites.

                // Add it to the deactivatable array and enqueue automatic deactivation of this plugin.
                $this->deactivatable_plugins[$_conflicting_plugin_slug] = $this->App->Config->§conflicts['§deactivatable_plugins'][$_conflicting_plugin_slug];

                add_action('admin_init', function () use ($_conflicting_plugin_basename) {
                    deactivate_plugins($_conflicting_plugin_basename, false, false);
                }, -10000); // With a very early priority.
            } else {
                $this->plugins[$_conflicting_plugin_slug] = $this->App->Config->§conflicts['§plugins'][$_conflicting_plugin_slug];
            }
        } // unset($_active_plugin_basename, $_active_plugin_slug, $_conflicting_plugin_basename, $_conflicting_plugin_slug);

        foreach ($all_active_theme_slugs as $_active_theme_slug) { // Note that one of these could be empty.
            if ($_active_theme_slug === $this->App->base_dir_basename && $this->App->Config->§specs['§type'] === 'theme') {
                continue; // Sanity check. Cannot depend on self of the same type.
            }
            if ($_active_theme_slug && $conflicting_theme_slugs && in_array($_active_theme_slug, $conflicting_theme_slugs, true)) {
                $_conflicting_theme_slug                = &$_active_theme_slug;
                $this->themes[$_conflicting_theme_slug] = $this->App->Config->§conflicts['§themes'][$_conflicting_theme_slug];
            }
        } // unset($_active_theme_slug, $_conflicting_theme_slug); // Housekeeping.

        $conflicts_exist = $this->exist(); // Conflicts exist?

        if ($is_front_or_ajax && !$conflicts_exist) {
            $this->lastOkTime(time());
        } elseif ($conflicts_exist) {
            $this->lastOkTime(0);
            $this->maybeNotify();
        }
    }

    /**
     * Last OK time.
     *
     * @since 160525 Dependencies.
     *
     * @param int|null $time Last check time.
     *
     * @return int Last OK time.
     */
    protected function lastOkTime(int $time = null): int
    {
        $key             = $this->App->Config->©brand['©var'].'_conflicts_last_ok_time';
        $is_network_wide = $this->App->Config->§specs['§is_network_wide'];

        if ($is_network_wide && is_multisite()) {
            if (isset($time)) {
                update_network_option(null, $key, $time);
            }
            return (int) get_network_option(null, $key);
        } else {
            if (isset($time)) {
                update_option($key, $time);
            }
            return (int) get_option($key);
        }
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 160525 Initial release.
     *
     * @note Intentionally choosing not to use built-in notice utilities here.
     *  The notice utilities set option values, and if we are in conflict with another
     *  application (e.g., lite/pro edition) that could lead to unforeseen problems.
     *
     * @note Not only that, but the hooks needed to use notice utilities are not attached
     * until after a check for conflicts has been finalized; i.e., notice utils won't work anyway.
     */
    protected function maybeNotify()
    {
        if (!is_admin()) {
            return; // Not applicable.
        } elseif (!$this->exist()) {
            return; // No conflicts.
        }
        if ($this->plugins) {
            add_action('all_admin_notices', function () {
                if (!current_user_can('activate_plugins')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-error">'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> is not running yet. A conflicting plugin, <strong>%2$s</strong>, is currently active. Please deactivate the \'%2$s\' plugin to clear this message.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html(end($this->plugins))).'</p>'.
                     '</div>';
            });
        } elseif ($this->themes) {
            add_action('all_admin_notices', function () {
                if (!current_user_can('switch_themes')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-error">'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> is not running yet. A conflicting theme, <strong>%2$s</strong>, is currently active. Please switch your \'%2$s\' theme to clear this message.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html(end($this->themes))).'</p>'.
                     '</div>';
            });
        } elseif ($this->deactivatable_plugins) {
            add_action('all_admin_notices', function () {
                if (!current_user_can('activate_plugins')) {
                    return; // Do not show.
                }
                echo '<div class="notice notice-warning">'.
                        '<p>'.sprintf(__('The following plugins were deactivated automatically because they conflict with <strong>%1$s</strong>. Deactivated: <em>%2$s</em>; in favor of <strong>%1$s</strong>.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html(implode(', ', $this->deactivatable_plugins))).'</p>'.
                        '<p>'.sprintf(__('<strong>%1$s</strong> will be able load now. Please <a href="javascript:location.reload();">refresh</a>.', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name'])).'</p>'.
                     '</div>';
            });
        }
    }
}
