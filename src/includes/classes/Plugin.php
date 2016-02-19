<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils\Plugin as Utils;
#
use WebSharks\WpSharks\Core\Functions as wc;
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
 * Plugin.
 *
 * @since 16xxxx Initial release.
 */
abstract class Plugin extends CoreClasses\AbsCore
{
    /**
     * Namespace.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $namespace;

    /**
     * Namespace SHA-1.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $namespace_sha1;

    /**
     * Dir.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $dir;

    /**
     * Core dir.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $core_dir;

    /**
     * Config.
     *
     * @since 16xxxx
     *
     * @type PluginConfig
     */
    public $Config;

    /**
     * Dicer.
     *
     * @since 16xxxx
     *
     * @type PluginDi
     */
    public $Di;

    /**
     * Utilities.
     *
     * @since 16xxxx
     *
     * @type PluginUtils
     */
    public $Utils;

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
        parent::__construct();

        $Class = new \ReflectionClass($this);

        $this->namespace      = $Class->getNamespaceName();
        $this->namespace_sha1 = sha1($this->namespace);

        $this->dir      = dirname($Class->getFileName(), 4);
        $this->core_dir = dirname(__FILE__, 4);

        $this->Config = new PluginConfig($this, $instance_base, $instance);
        $this->Di     = new PluginDi($this, $this->Config->di['default_rule']);
        $this->Utils  = new PluginUtils($this); // Utility class access.

        $GLOBALS[$Class->getName()]                = $this;
        $GLOBALS[$this->Config->brand['var_base']] = $this;

        $this->Di->addInstances([
            $this,
            $this->Config,
            $this->Utils,
        ]);
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
