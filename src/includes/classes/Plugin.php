<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils;
use WebSharks\WpSharks\Core\Functions as w;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin.
 *
 * @since 16xxxx Initial release.
 */
class Plugin extends CoreClasses\AbsCore
{
    /**
     * Conflicts?
     *
     * @since 16xxxx
     *
     * @type array Slugs.
     */
    protected $conflicts = [];

    /**
     * Version.
     *
     * @since 16xxxx
     *
     * @type string Version.
     */
    const VERSION = '160217'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $instance_base Instance base.
     * @param array $instance      Instance args (highest precedence).
     */
    public function __construct(array $instance_base, array $instance = [])
    {
        $GLOBALS[self::class] = $this;

        $plugin_type = (string) ($instance_base['plugin']['type'] ?? '');
        $plugin_file = (string) ($instance_base['plugin']['file'] ?? '');

        $plugin_acronym = (string) ($instance_base['plugin']['acronym'] ?? '');

        $plugin_slug      = (string) ($instance_base['plugin']['slug'] ?? '');
        $plugin_base_slug = (string) ($instance_base['plugin']['base_slug'] ?? preg_replace('/\-+(?:lite|pro)$/ui', '', $plugin_slug));

        $plugin_var      = (string) ($instance_base['plugin']['var'] ?? str_replace('-', '_', $plugin_slug));
        $plugin_base_var = (string) ($instance_base['plugin']['base_var'] ?? preg_replace('/_+(?:lite|pro)$/ui', '', $plugin_var));

        $plugin_name      = (string) ($instance_base['plugin']['name'] ?? '');
        $plugin_base_name = (string) ($instance_base['plugin']['base_name'] ?? preg_replace('/\s+(?:lite|pro)$/ui', '', $plugin_name));

        $plugin_domain      = (string) ($instance_base['plugin']['domain'] ?? '');
        $plugin_text_domain = (string) ($instance_base['plugin']['text_domain'] ?? $plugin_base_slug);

        $plugin_qv_prefix        = (string) ($instance_base['plugin']['qv_prefix'] ?? mb_strtolower($plugin_acronym).'_');
        $plugin_transient_prefix = (string) ($instance_base['plugin']['transient_prefix'] ?? $plugin_qv_prefix);

        $plugin_is_pro = (bool) ($instance_base['plugin']['is_pro'] ?? preg_match('/\-pro$/ui', $plugin_slug));

        $default_instance_base = [
            'type' => $plugin_type,
            'file' => $plugin_file,

            'acronym' => $plugin_acronym,

            'slug'      => $plugin_slug,
            'base_slug' => $plugin_base_slug,

            'var'      => $plugin_var,
            'base_var' => $plugin_base_var,

            'name'      => $plugin_name,
            'base_name' => $plugin_base_name,

            'domain'      => $plugin_domain,
            'text_domain' => $plugin_text_domain,

            'qv_prefix'        => $plugin_qv_prefix,
            'transient_prefix' => $plugin_transient_prefix,

            'is_pro' => $plugin_is_pro, // Is this a pro version?

            'conflicts' => [ // Keys are plugin slugs, values are plugin names.
                $plugin_base_slug.($plugin_is_pro ? '' : '-pro') => $plugin_base_name.($plugin_is_pro ? ' Lite' : ' Pro'),
            ],
            'deactivatble_conflicts' => [ // Keys are plugin slugs, values are plugin names.
                $plugin_base_slug.($plugin_is_pro ? '' : '-pro') => $plugin_base_name.($plugin_is_pro ? ' Lite' : ' Pro'),
            ],
            'notices' => [
                'key' => $blog_salt,
            ],
        ];
        $instance_base = $this->merge($default_instance_base, $instance_base);

        parent::__construct($instance_base, $instance);

        $this->maybeCheckConflicts();
        $this->maybeEnqueueConflictsNotice();
    }

    /**
     * Plugin conflicts exist?
     *
     * @since 16xxxx Initial release.
     *
     * @return bool True if has conflicts.
     */
    protected function hasConflicts(): bool
    {
        return !empty($this->conflicts);
    }

    /**
     * Check for plugin conflicts.
     *
     * @since 16xxxx Initial release.
     */
    protected function maybeCheckConflicts()
    {
        if (!$this->Config->plugin['conflicts']) {
            return; // Nothing to do here.
        }
        $active_plugins           = (array) get_option('active_plugins', []);
        $active_sitewide_plugins  = is_multisite() ? array_keys((array) get_site_option('active_sitewide_plugins', [])) : [];
        $active_plugins           = array_unique(array_merge($active_plugins, $active_sitewide_plugins));
        $conflicting_plugin_slugs = array_keys($this->Config->plugin['conflicts']);

        foreach ($active_plugins as $_active_plugin_basename) {
            $_active_plugin_slug = mb_strstr($_active_plugin_basename, '/', true);

            if ($_active_plugin_slug === $this->Config->plugin['slug']) {
                continue; // Sanity check. Cannot conflict w/ self.
            }
            if (in_array($_active_plugin_slug, $conflicting_plugin_slugs, true)) {
                if (in_array($_active_plugin_slug, $this->Config->plugin['deactivatble_conflicts'], true)) {
                    add_action('admin_init', function () use ($_active_plugin_basename) {
                        if (function_exists('deactivate_plugins')) {
                            deactivate_plugins($_active_plugin_basename, true);
                        }
                    }, -1000);
                } else {
                    $_conflicting_plugin_name              = $this->Config->plugin['conflicts'][$_active_plugin_slug];
                    $this->conflicts[$_active_plugin_slug] = $_conflicting_plugin_name; // `slug` => `name`.
                }
            }
        } // unset($_active_plugin_basename, $_active_plugin_slug, $_conflicting_plugin_name);
    }

    /**
     * Maybe enqueue dashboard notice.
     *
     * @since 16xxxx Rewrite.
     */
    protected function maybeEnqueueConflictsNotice()
    {
        if (!$this->conflicts) {
            return; // No conflicts.
        }
        // @TODO enhance this w/ notice utils.

        add_action('all_admin_notices', function () {
            echo '<div class="error">'.// Error notice.
                 '   <p>'.// Running one or more conflicting plugins at the same time.
                 '      '.sprintf(__('<strong>%1$s</strong> is NOT running. A conflicting plugin, <strong>%2$s</strong>, is currently active. Please deactivate the %2$s plugin to clear this message.', SLUG_TD), esc_html($this_plugin_name), esc_html($conflicting_plugin_name)).
                 '   </p>'.
                 '</div>';
        });
    }
}
