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
     * App.
     *
     * @since 16xxxx
     *
     * @type App
     */
    protected $App;

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
     * Setup?
     *
     * @since 16xxxx
     *
     * @type bool
     */
    public $is_setup = false;

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

        $this->App = $GLOBALS[App::class];

        $Class = new \ReflectionClass($this);

        $this->namespace      = $Class->getNamespaceName();
        $this->namespace_sha1 = sha1($this->namespace);

        $this->dir      = dirname($Class->getFileName(), 4);
        $this->core_dir = dirname(__FILE__, 4);

        $this->Config = new PluginConfig($this, $instance_base, $instance);
        $this->Di     = new PluginDi($this, $this->Config->di['default_rule']);
        $this->Utils  = new PluginUtils($this); // Utility class access.

        $GLOBALS[$Class->getName()]                = $this;
        $GLOBALS[$this->Config->brand['var']]      = $this;
        $GLOBALS[$this->Config->brand['base_var']] = $this;

        $this->Di->addInstances([
            $this,
            $this->Config,
            $this->Utils,
        ]);
        if (!$this->Utils->Conflicts->exist()) {
            add_action('after_setup_theme', [$this, 'setup'], $this->Config->setup['priority']);
        }
    }

    /**
     * Setup handler.
     *
     * @since 16xxxx Initial release.
     */
    public function setup()
    {
        if ($this->is_setup) {
            return;
        }
        $this->is_setup = true;

        if (!$this->Config->setup['enable_hooks']) {
            return; // No hooks.
        }
        add_action('all_admin_notices', [$this->Utils->Notices, 'display']);
    }

    /**
     * Apply filters.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $hook  A hook.
     * @param mixed  $value Value to filter.
     * @param mixed ...$args Any additional args.
     *
     * @return mixed Filtered `$value`.
     */
    public function applyFilters(string $hook, $value, ...$args)
    {
        return apply_filters($this->Config->brand['base_var'].'_'.$hook, $value, ...$args);
    }

    /**
     * Do an action.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $hook A hook.
     * @param mixed ...$args Any additional args.
     */
    public function doAction(string $hook, ...$args)
    {
        do_action($this->Config->brand['base_var'].'_'.$hook, ...$args);
    }
}
