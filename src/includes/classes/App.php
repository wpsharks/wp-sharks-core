<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * App (plugins must extend).
 *
 * @since 16xxxx Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * Type.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $type;

    /**
     * File.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $file;

    /**
     * Default options.
     *
     * @since 16xxxx
     *
     * @type array
     */
    public $default_options;

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
    const VERSION = '160224'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array    $instance_base Instance base.
     * @param array    $instance      Instance args (highest precedence).
     * @param App|null $parent        Parent app (optional).
     * @param array    $args          Any additional behavioral args.
     */
    public function __construct(array $instance_base = [], array $instance = [], App $parent = null, array $args = [])
    {
        $default_args = [
            'validate_brand' => true,
        ];
        $args = array_merge($default_args, $args);

        $Class = new \ReflectionClass($this);

        $this->class     = $Class->getName();
        $this->namespace = $Class->getNamespaceName();

        $this->dir          = dirname($Class->getFileName(), 4);
        $this->dir_basename = basename($this->dir);

        if (is_file($this->dir.'/plugin.php')) {
            $this->type = 'plugin';
            $this->file = $this->dir.'/plugin.php';
        } elseif (is_file($this->dir.'/'.$this->dir_basename.'.php')) {
            $this->type = 'plugin';
            $this->file = $this->dir.'/'.$this->dir_basename.'.php';
        } elseif (is_file($this->dir.'/style.css')) {
            $this->type = 'theme';
            $this->file = $this->dir.'/style.css';
        } else {
            throw new Exception('Unable to determine type/file.');
        }
        if ($this->class === self::class) {
            $brand = array_merge(
                [
                    'slug'    => 'wp-sharks-core',
                    'var'     => 'wp_sharks_core',
                    'name'    => 'WP Sharks Core',
                    'acronym' => 'WPSC',
                    'prefix'  => 'wpsc',

                    'domain'      => 'wpsharks.com',
                    'domain_path' => '/product/core',
                    'text_domain' => 'wp-sharks-core',
                    'is_pro'      => false,
                ],
                $instance_base['brand'] ?? [],
                $instance['brand'] ?? []
            );
        } else {
            $brand = array_merge(
                [
                    'slug'    => '',
                    'var'     => '',
                    'name'    => '',
                    'acronym' => '',
                    'prefix'  => '',

                    'domain'      => '',
                    'domain_path' => '',
                    'text_domain' => '',
                    'is_pro'      => null,
                ],
                $instance_base['brand'] ?? [],
                $instance['brand'] ?? []
            );
            if (!isset($GLOBALS[self::class])) {
                throw new Exception('Missing core instance.');
            }
            $parent = $parent ?? $GLOBALS[self::class];

            if (empty($brand['slug'])) {
                $brand['slug'] = $this->dir_basename;
                $brand['slug'] = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $brand['slug']);
            } elseif ($args['validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['slug'])) {
                throw new Exception('Please remove `lite|pro` suffix from slug.');
            }
            if (empty($brand['var'])) {
                $brand['var'] = $parent->Utils->Slug->toVar($brand['slug']);
            } elseif ($args['validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['var'])) {
                throw new Exception('Please remove `lite|pro` suffix from var.');
            }
            if (empty($brand['name'])) {
                $brand['name'] = $parent->Utils->Slug->toName($brand['slug']);
            } elseif ($args['validate_brand'] && preg_match('/\s+(?:Lite|Pro)$/ui', $brand['name'])) {
                throw new Exception('Please remove `Lite|Pro` suffix from name.');
            }
            if (empty($brand['acronym'])) {
                $brand['acronym'] = $parent->Utils->Name->toAcronym($brand['name']);
            } elseif ($args['validate_brand'] && preg_match('/(?:LITE|PRO)$/ui', $brand['acronym'])) {
                throw new Exception('Please remove `LITE|PRO` suffix from acronym.');
            }
            if (empty($brand['prefix'])) {
                $brand['prefix'] = $parent->Utils->Name->toSlug($brand['acronym']);
            } elseif ($args['validate_brand'] && preg_match('/\s+(?:lite|pro)$/ui', $brand['prefix'])) {
                throw new Exception('Please remove `lite|pro` suffix from prefix.');
            }
            if (empty($brand['domain'])) {
                $brand['domain']      = $parent->Config->brand['domain'];
                $brand['domain_path'] = '/product/'.$brand['slug'];
            }
            if (empty($brand['text_domain'])) {
                $brand['text_domain'] = $brand['slug'];
            } elseif ($args['validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['text_domain'])) {
                throw new Exception('Please remove `lite|pro` suffix from text_domain.');
            }
            if (!isset($brand['is_pro'])) {
                $brand['is_pro'] = stripos($this->namespace, '\\Pro\\') !== false;
            }
        }
        $site_tmp_dir = rtrim(get_temp_dir(), '/').'/'.sha1(site_url());

        $default_instance_base = [
            'brand' => [
                'slug'    => '',
                'var'     => '',
                'name'    => '',
                'acronym' => '',
                'prefix'  => '',

                'domain'      => '',
                'domain_path' => '',
                'text_domain' => '',
                'is_pro'      => null,
            ],

            'di' => [
                'default_rule' => [
                    'new_instances' => [
                    ],
                ],
            ],

            'setup' => [
                'enable'       => true,
                'priority'     => -100,
                'enable_hooks' => true,
            ],

            'db' => [
                'tables_dir' => '%%app_dir%%/src/includes/tables',
            ],

            'fs_paths' => [
                'logs_dir'  => $site_tmp_dir.'/'.$brand['slug'].'/logs',
                'cache_dir' => $site_tmp_dir.'/'.$brand['slug'].'/cache',
            ],

            'keys' => [
                'salt' => str_pad(wp_salt(), 64, 'x'),
            ],

            'conflicting' => [
                'plugins'              => [],
                'themes'               => [],
                'deactivatble_plugins' => [],
            ],

            'caps' => [
                'manage' => 'activate_plugins',
            ],

            'options'         => [],
            'pro_option_keys' => [],

            'notices' => [
                'on_install'   => [],
                'on_reinstall' => [],
            ],
        ];
        if ($this->type === 'plugin') {
            $lp_conflicting_base = $brand['slug'].($brand['is_pro'] ? '' : '-pro');
            $lp_conflicting_name = $brand['name'].($brand['is_pro'] ? ' Lite' : ' Pro');

            $default_instance_base['conflicting']['plugins'][$lp_conflicting_base]              = $lp_conflicting_name;
            $default_instance_base['conflicting']['deactivatble_plugins'][$lp_conflicting_base] = $lp_conflicting_name;
        }
        $instance_base         = $this->merge($default_instance_base, $instance_base);
        $this->default_options = &$instance_base['options']; // Collective default options.
        // Note: The `$this->default_options` property is required by `mergeOptions()` below.

        if (!is_array($site_options = get_option($brand['var'].'_options'))) {
            update_option($brand['var'].'_options', $site_options = []);
        }
        $options = $this->mergeOptions($instance_base['options'], $site_options);
        $options = $this->mergeOptions($options, $instance['options']);

        $instance['brand']   = &$brand; // Force brand by reference.
        $instance['options'] = &$options; // Force by reference.

        $instance            = apply_filters($brand['var'].'_instance', $instance);
        $instance['options'] = apply_filters($brand['var'].'_options', $instance['options']);

        parent::__construct($instance_base, $instance, $parent, $args);

        if ($this->Utils->Conflicts->exist()) {
            return; // Stop on conflicts!
        }
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            $this->Utils->Installer->checkVersion();
        }
        $GLOBALS[$this->Config->brand['var']] = $this->Facades;

        if ($this->Config->setup['enable']) {
            add_action('after_setup_theme', [$this, 'onAfterSetupTheme'], $this->Config->setup['priority']);
        }
    }

    /**
     * Update options.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $new_options New options.
     *
     * @note `null` options force a default value.
     */
    public function updateOptions(array $new_options)
    {
        $this->options = $this->mergeOptions($this->options, $new_options);
        update_option($this->brand['var'].'_options', $this->options);
    }

    /**
     * Merge options.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The resuling array after merging.
     *
     * @note `null` options force a default value.
     */
    protected function mergeOptions(array $base, array $merge): array
    {
        $options = array_merge($base, $merge);
        $options = array_intersect_key($options, $this->default_options);

        foreach ($this->default_options as $_key => $_default_option_value) {
            if (is_null($options[$_key])) {
                $options[$_key] = $_default_option_value;
            } else {
                settype($options[$_key], gettype($_default_option_value));
            }
        } // unset($_key, $_default_option_value);

        return $options;
    }

    /**
     * Setup handler.
     *
     * @since 16xxxx Initial release.
     */
    public function onAfterSetupTheme()
    {
        if (!$this->Config->setup['enable']) {
            return; // Disabled.
        }
        if ($this->is_setup) {
            return; // Done.
        }
        $this->is_setup = true;

        if ($this->Config->setup['enable_hooks']) {
            add_action('admin_init', [$this->Utils->Options, 'onAdminInitMaybeSave']);
            add_action('admin_init', [$this->Utils->Options, 'onAdminInitMaybeRestoreDefaults']);

            add_action('admin_init', [$this->Utils->Notices, 'onAdminInitMaybeDismiss']);
            add_action('all_admin_notices', [$this->Utils->Notices, 'onAllAdminNotices']);
        }
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
        return apply_filters($this->Config->brand['var'].'_'.$hook, $value, ...$args);
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
        do_action($this->Config->brand['var'].'_'.$hook, ...$args);
    }
}
