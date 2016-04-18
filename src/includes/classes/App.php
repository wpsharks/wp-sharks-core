<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
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
     * Version.
     *
     * @since 16xxxx
     *
     * @type string Version.
     */
    const VERSION = '160417'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param array            $instance_base Instance base.
     * @param array            $instance      Instance args.
     * @param Classes\App|null $parent        Parent app (optional).
     * @param array            $args          Any additional behavioral args.
     */
    public function __construct(array $instance_base = [], array $instance = [], Classes\App $parent = null, array $args = [])
    {
        # Establish arguments.

        $default_args = [
            '©use_server_cfgs' => false,
            '§validate_brand'  => true,
        ];
        $args = array_merge($default_args, $args);

        # Define a few reflection-based properties.

        $this->reflection = new \ReflectionClass($this);

        $this->class     = $this->reflection->getName();
        $this->namespace = $this->reflection->getNamespaceName();

        $this->base_dir          = dirname($this->reflection->getFileName(), 4);
        $this->base_dir_basename = basename($this->base_dir);

        # Establish specs & brand for parent constructor.

        if ($this->class === self::class) {
            $specs = array_merge(
                [
                    '§is_pro'          => false,
                    '§is_network_wide' => false,
                    '§type'            => 'plugin',
                    '§file'            => $this->base_dir.'/plugin.php',
                ],
                $instance_base['§specs'] ?? [],
                $instance['§specs'] ?? []
            );
            $brand = array_merge(
                [
                    '©slug'        => 'wp-sharks-core',
                    '©text_domain' => 'wp-sharks-core',
                    '©var'         => 'wp_sharks_core',
                    '©name'        => 'WP Sharks Core',
                    '©acronym'     => 'WPSC',
                    '©prefix'      => 'wpsc',

                    '§domain'      => 'wpsharks.com',
                    '§domain_path' => '/product/core',
                ],
                $instance_base['©brand'] ?? [],
                $instance['©brand'] ?? []
            );
        } else {
            if (!isset($GLOBALS[self::class])) {
                throw new Exception('Missing core instance.');
            }
            $parent = $parent ?? $GLOBALS[self::class];

            $specs = array_merge(
                [
                    '§is_pro'          => null,
                    '§is_network_wide' => false,
                    '§type'            => '',
                    '§file'            => '',
                ],
                $instance_base['§specs'] ?? [],
                $instance['§specs'] ?? []
            );
            if (!isset($specs['§is_pro'])) {
                $specs['§is_pro'] = mb_stripos($this->namespace, '\\Pro\\') !== false;
            }
            if (!$specs['§type'] || !$specs['§file']) {
                if (is_file($this->base_dir.'/plugin.php')) {
                    $specs['§type'] = 'plugin';
                    $specs['§file'] = $this->base_dir.'/plugin.php';
                } elseif (is_file($this->base_dir.'/'.$this->base_dir_basename.'.php')) {
                    $specs['§type'] = 'plugin';
                    $specs['§file'] = $this->base_dir.'/'.$this->base_dir_basename.'.php';
                } elseif (is_file($this->base_dir.'/style.css')) {
                    $specs['§type'] = 'theme';
                    $specs['§file'] = $this->base_dir.'/style.css';
                } else {
                    throw new Exception('Unable to determine type/file.');
                }
            }
            $brand = array_merge(
                [
                    '©slug'        => '',
                    '©text_domain' => '',
                    '©var'         => '',
                    '©name'        => '',
                    '©acronym'     => '',
                    '©prefix'      => '',

                    '§domain'      => '',
                    '§domain_path' => '',
                ],
                $instance_base['©brand'] ?? [],
                $instance['©brand'] ?? []
            );
            if (!$brand['©slug']) {
                $brand['©slug'] = $this->base_dir_basename;
                $brand['©slug'] = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©slug'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©slug.');
            }
            if (!$brand['©text_domain']) {
                $brand['©text_domain'] = $brand['©slug'];
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©text_domain'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©text_domain.');
            }
            if (!$brand['©var']) {
                $brand['©var'] = $parent->c::slugToVar($brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/[_\-]+(?:lite|pro)$/ui', $brand['©var'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©var.');
            }
            if (!$brand['©name']) {
                $brand['©name'] = $parent->c::slugToName($brand['©slug']);
            } elseif ($args['§validate_brand'] && preg_match('/\s+(?:Lite|Pro)$/ui', $brand['©name'])) {
                throw new Exception('Please remove `Lite|Pro` suffix from ©name.');
            }
            if (!$brand['©acronym']) {
                $brand['©acronym'] = $parent->c::nameToAcronym($brand['©name']);
            } elseif ($args['§validate_brand'] && preg_match('/(?:LITE|PRO)$/ui', $brand['©acronym'])) {
                throw new Exception('Please remove `LITE|PRO` suffix from ©acronym.');
            }
            if (!$brand['©prefix']) {
                $brand['©prefix'] = $parent->c::nameToSlug($brand['©acronym']);
            } elseif ($args['§validate_brand'] && preg_match('/\s+(?:lite|pro)$/ui', $brand['©prefix'])) {
                throw new Exception('Please remove `lite|pro` suffix from ©prefix.');
            } elseif ($args['§validate_brand'] && preg_match('/[^a-z0-9]/u', $brand['©prefix'])) {
                throw new Exception('Please remove `[^a-z0-9]` chars from ©prefix.');
            }
            if (!$brand['§domain']) {
                $brand['§domain']      = $parent->Config->©brand['§domain'];
                $brand['§domain_path'] = '/product/'.$brand['©slug'];
            }
        }
        # Build the core/default instance base.

        if (!($wp_tmp_dir = rtrim(get_temp_dir(), '/'))) {
            throw new Exception('Failed to acquire a temp directory.');
        }
        if (!($wp_salt = wp_salt()) || !($wp_salt_key = hash('sha256', $wp_salt.$brand['©slug']))) {
            throw new Exception('Failed to generate a unique salt/key.');
        }
        $default_instance_base = [
            '§specs' => [
                '§is_pro'          => false,
                '§is_network_wide' => false,
                '§type'            => '',
                '§file'            => '',
            ],

            '©brand' => [
                '©text_domain' => '',
                '©slug'        => '',
                '©var'         => '',
                '©name'        => '',
                '©acronym'     => '',
                '©prefix'      => '',

                '§domain'      => '',
                '§domain_path' => '',
            ],

            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                    ],
                ],
            ],
            '©sub_namespace_map' => [
                'SCore' => [
                    '©utils'   => '§',
                    '©facades' => 's',
                ],
            ],

            '§setup' => [
                '§enable'       => true,
                '§enable_hooks' => true,
                '§complete'     => false,
            ],

            '§db' => [
                '§tables_dir' => '%%app_base_dir%%/src/includes/tables',
            ],

            '©fs_paths' => [
                '©logs_dir'   => $wp_tmp_dir.'/'.$brand['©slug'].'/logs',
                '©cache_dir'  => $wp_tmp_dir.'/'.$brand['©slug'].'/cache',
                '©errors_dir' => '', '©config_file' => '', // N/A.
            ],

            '§keys' => [
                '§salt' => $wp_salt_key,
            ],
            '©cookies' => [
                '©encryption_key' => $wp_salt_key,
            ],
            '©hash_ids' => [
                '©hash_key' => $wp_salt_key,
            ],
            '©passwords' => [
                '©hash_key' => $wp_salt_key,
            ],

            '§conflicting' => [
                '§plugins'               => [],
                '§themes'                => [],
                '§deactivatable_plugins' => [],
            ],

            '§caps' => [
                '§manage' => 'activate_plugins',
            ],

            '§default_options' => [],
            '§options'         => [],
            '§pro_option_keys' => [],

            '§notices' => [
                '§on_install' => function ($installion_history) {
                    return [
                        'is_transient' => true,
                        'markup'       => sprintf(
                            __('<strong>%1$s</strong> v%2$s installed successfully.', 'wp-sharks-core'),
                            esc_html($this->Config->©brand['©name']),
                            esc_html($this->c::version())
                        ),
                    ];
                },
                '§on_reinstall' => function ($installion_history) {
                    return [
                        'is_transient' => false,
                        'markup'       => sprintf(
                            __('<strong>%1$s</strong> detected a new version of itself &amp; recompiled successfully. You\'re now running v%2$s.', 'wp-sharks-core'),
                            esc_html($this->Config->©brand['©name']),
                            esc_html($this->c::version())
                        ),
                    ];
                },
            ],

            '§uninstall' => false,
        ];
        if ($specs['§type'] === 'plugin') {
            $lp_conflicting_basename_slug = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $this->base_dir_basename);
            $lp_conflicting_basename_slug .= ($specs['§is_pro'] ? '' : '-pro');

            $lp_conflicting_basename_slug = $brand['©slug'].($specs['§is_pro'] ? '' : '-pro');
            $lp_conflicting_name          = $brand['©name'].($specs['§is_pro'] ? ' Lite' : ' Pro');

            $default_instance_base['§conflicting']['§plugins'][$lp_conflicting_basename_slug]               = $lp_conflicting_name;
            $default_instance_base['§conflicting']['§deactivatable_plugins'][$lp_conflicting_basename_slug] = $lp_conflicting_name;
        }
        # Build collective instance base & instance, then run parent constructor.

        $instance_base           = $this->mergeConfig($default_instance_base, $instance_base);
        $instance_base['§specs'] = &$specs; // Already established (in full) above.
        $instance_base['©brand'] = &$brand; // Already established (in full) above.

        unset($instance['§specs'], $instance['©brand']);
        $instance = apply_filters($brand['©var'].'_instance', $instance, $instance_base);

        parent::__construct($instance_base, $instance, $parent, $args);

        # Merge site owner options (highest precedence).

        if ($this->Config->§specs['§is_network_wide'] && is_multisite()) {
            if (!is_array($site_owner_options = get_network_option(null, $this->Config->©brand['©var'].'_options'))) {
                update_network_option(null, $this->Config->©brand['©var'].'_options', $site_owner_options = []);
            }
        } elseif (!is_array($site_owner_options = get_option($this->Config->©brand['©var'].'_options'))) {
            update_option($this->Config->©brand['©var'].'_options', $site_owner_options = []);
        }
        $this->Config->§options = $this->s::mergeOptions($this->Config->§options, $site_owner_options);
        $this->Config->§options = $this->s::applyFilters('options', $this->Config->§options);

        # Post-construct sub-routines.

        if ($this->s::conflictsExist()) {
            return; // Stop here.
        }
        load_plugin_textdomain($this->Config->©brand['©text_domain']);

        if ($this->Config->§uninstall) {
            $this->s::maybeUninstall();
            return; // Stop here.
        }
        $this->s::maybeInstall(); // Maybe install (or reinstall).

        $GLOBALS[$this->Config->©brand['©var']] = $this->facades['a'];

        $this->onPluginsLoadedMaybeSetupHooks(); // Maybe setup hooks.
    }

    /**
     * Maybe setup hooks.
     *
     * @since 16xxxx Initial release.
     *
     * @note Called by constructor, which attaches to `plugins_loaded` hook in WP core.
     */
    public function onPluginsLoadedMaybeSetupHooks()
    {
        if ($this->Config->§uninstall) {
            return; // Uninstalling.
        }
        if (!$this->Config->§setup['§enable']) {
            return; // Setup disabled.
        }
        if ($this->Config->§setup['§complete']) {
            return; // Already complete.
        }
        $this->Config->§setup['§complete'] = true;

        if (!$this->Config->§setup['§enable_hooks']) {
            return; // Hooks disabled right now.
        }
        add_action('admin_init', [$this->Utils->§Options, 'onAdminInitMaybeSave']);
        add_action('admin_init', [$this->Utils->§Options, 'onAdminInitMaybeRestoreDefaults']);

        add_action('admin_init', [$this->Utils->§Notices, 'onAdminInitMaybeDismiss']);
        add_action('all_admin_notices', [$this->Utils->§Notices, 'onAllAdminNotices']);

        $this->onPluginsLoadedSetupHooks(); // For extenders.
    }

    /**
     * Hook setup handler.
     *
     * @since 16xxxx Initial release.
     *
     * @note Called by constructor, which attaches to `plugins_loaded` hook in WP core.
     */
    protected function onPluginsLoadedSetupHooks()
    {
        // For extenders; only called when setup should run.
    }
}
