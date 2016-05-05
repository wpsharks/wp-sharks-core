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
    const VERSION = '160504'; //v//

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
                $brand['©slug'] = preg_replace('/^wp(?:sc|[_\-]+sharks)?[_\-]+/ui', '', $brand['©slug']);
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
                $brand['©prefix'] = mb_strtolower($brand['©acronym']);
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
        # Collect essential WordPress config values.
        // NOTE: These are not 100% compatible with `switch_to_blog()`.
        // These represent values for the initial/current site.

        $wp_is_multisite = is_multisite();

        if (!($wp_tmp_dir = rtrim(get_temp_dir(), '/'))) {
            throw new Exception('Failed to acquire a temp directory.');
        }
        if (!($wp_salt_key = hash('sha256', wp_salt().$brand['©slug']))) {
            throw new Exception('Failed to generate a unique salt/key.');
        }
        if (!($wp_site_url_option = parse_url(get_option('siteurl')))) {
            throw new Exception('Failed to parse site URL option.');
        }
        if (!($wp_site_default_scheme = $wp_site_url_option['scheme'] ?? 'http')) {
            throw new Exception('Failed to parse site URL option scheme.');
        }
        if ($specs['§type'] === 'theme') { // Special case.
            if (!($wp_app_url = parse_url(get_template_directory_uri()))) {
                throw new Exception('Failed to parse theme dir URL.');
            }
        } elseif (!($wp_app_url = parse_url(plugin_dir_url($specs['§file'])))) {
            throw new Exception('Failed to parse plugin dir URL.');
        }
        if (!($wp_app_url_host = $wp_app_url['host'] ?? (string) @$_SERVER['HTTP_HOST'])) {
            throw new Exception('Failed to parse app URL host.');
        }
        if (!($wp_app_url_root_host = implode('.', array_slice(explode('.', $wp_app_url_host), -2)))) {
            throw new Exception('Failed to parse app URL root host.');
        }
        $wp_app_url_path = rtrim($wp_app_url['path'] ?? '', '/'); // Allowed to be empty.

        # Build the core/default instance base.

        $default_instance_base = [
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

            '©urls' => [
                '©hosts' => [
                    '©roots' => [
                        '©app' => $wp_app_url_root_host,
                    ],
                    '©app' => $wp_app_url_host,
                ],
                '©base_paths' => [
                    '©app' => $wp_app_url_path.'/src',
                ],
                '©default_scheme' => $wp_site_default_scheme,
                '©sig_key'        => $wp_salt_key,
            ],

            '§setup' => [ // On (or after): `plugins_loaded`.
                // Default is `after_setup_theme` for flexibility.
                '§hook'          => 'after_setup_theme',
                '§hook_priority' => -1000,

                // Other setup flags.
                '§enable_hooks' => true,

                // Systematic setup flags.
                '§complete' => false,
            ],

            '§database' => [
                '§tables_dir' => $this->base_dir.'/src/includes/tables',
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

            '§conflicts' => [
                '§plugins' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
                '§themes' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
                '§deactivatable_plugins' => [
                    /*
                        '[slug]'  => '[name]',
                    */
                ],
            ],
            '§dependencies' => [
                '§plugins' => [
                    /*
                        '[slug]' => [
                            'name'        => '',
                            'url'         => '',
                            'archive_url' => '',
                            'in_wp'       => true,
                            'test'        => function(string $slug) {},

                            A test function is optional.
                            A successful test must return `true`.
                            A failed test must return an array with:
                                - `reason`      = One of: `needs-upgrade|needs-downgrade`.
                                - `min_version` = Min version, if `reason=needs-upgrade`.
                                - `max_version` = Max version, if `reason=needs-downgrade`.
                        ],
                    */
                ],
                '§themes' => [
                    /*
                        '[slug]' => [
                            'name'        => '',
                            'url'         => '',
                            'archive_url' => '',
                            'in_wp'       => true,
                            'test'        => function(string $slug) {},

                            A test function is optional.
                            A successful test must return `true`.
                            A failed test must return an array with:
                                - `reason`      = One of: `needs-upgrade|needs-downgrade`.
                                - `min_version` = Min version, if `reason=needs-upgrade`.
                                - `max_version` = Max version, if `reason=needs-downgrade`.
                        ],
                    */
                ],
                '§others' => [
                    /*
                        '[arbitrary key]' => [
                            'name'        => '', // Short plain-text name; i.e., '[name]' Required
                            'description' => '', // Brief rich-text description; i.e., It requires [description].
                            'test'        => function(string $key) {},

                            A test function is required.
                            A successful test must return `true`.
                            A failed test must return an array with:
                                - `how_to_resolve` = Brief rich-text description; i.e., → To resolve, [how_to_resolve].
                                - `cap_to_resolve` = Cap required to satisfy; e.g., `manage_options`.
                        ],
                    */
                ],
            ],

            '§caps' => [
                '§manage' => $wp_is_multisite && $specs['§is_network_wide']
                    ? 'manage_network_plugins' : 'activate_plugins',
            ],

            '§pro_option_keys' => [],
            '§default_options' => [],
            '§options'         => [],

            '§notices' => [
                '§on_install' => function (array $installion_history) {
                    return [
                        'is_transient' => true,
                        'markup'       => sprintf(
                            __('<strong>%1$s</strong> v%2$s installed successfully.', 'wp-sharks-core'),
                            esc_html($this->Config->©brand['©name']),
                            esc_html($this->c::version())
                        ),
                    ];
                },
                '§on_reinstall' => function (array $installion_history) {
                    return [
                        'is_transient' => false,
                        'markup'       => sprintf(
                            __('<strong>%1$s</strong> detected a new version of itself. Recompiled successfully. You\'re now running v%2$s.', 'wp-sharks-core'),
                            esc_html($this->Config->©brand['©name']),
                            esc_html($this->c::version())
                        ),
                    ];
                },
            ],

            '§uninstall' => false,
        ];
        if ($specs['§type'] === 'plugin') {
            $lp_conflicting_name          = $brand['©name'].($specs['§is_pro'] ? ' Lite' : ' Pro');
            $lp_conflicting_basename_slug = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $this->base_dir_basename).($specs['§is_pro'] ? '' : '-pro');

            $default_instance_base['§conflicts']['§plugins'][$lp_conflicting_basename_slug]               = $lp_conflicting_name;
            $default_instance_base['§conflicts']['§deactivatable_plugins'][$lp_conflicting_basename_slug] = $lp_conflicting_name;
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
        $this->Config->§options = $this->s::mergeOptions($this->Config->§default_options, $this->Config->§options);
        $this->Config->§options = $this->s::mergeOptions($this->Config->§options, $site_owner_options);
        $this->Config->§options = $this->s::applyFilters('options', $this->Config->§options);

        # After-construct sub-routines are run now.

        // Sanity check; must be on (or after) `plugins_loaded` hook.
        // When uninstalling, must be on (or after) `init` hook.

        if (!did_action('plugins_loaded')) {
            throw new Exception('`plugins_loaded` action not done yet.');
        } elseif ($this->Config->§uninstall && !did_action('init')) {
            throw new Exception('`init` action not done yet.');
        }
        // Check for any known conflicts.

        if ($this->s::conflictsExist()) {
            return; // Stop here.
        }
        // Check for any outstanding dependencies.

        if ($this->s::dependenciesOutstanding()) {
            return; // Stop here.
        }
        // No known conflicts; load plugin text-domain.

        load_plugin_textdomain($this->Config->©brand['©text_domain']);

        // The rest of our sub-routines are based on the setup hook.

        if (did_action($this->Config->§setup['§hook'])) {
            $this->onSetup(); // Run setup immediately.
        } else { // Delay setup routines; i.e., attach to hook.
            add_action($this->Config->§setup['§hook'], [$this, 'onSetup'], $this->Config->§setup['§hook_priority']);
        }
    }

    /**
     * Run setup routines.
     *
     * @since 16xxxx Initial release.
     */
    public function onSetup() // Public hook.
    {
        if ($this->Config->§setup['§complete']) {
            return; // Already complete.
        }
        $this->Config->§setup['§complete'] = true;

        // Maybe setup early hooks.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->onSetupEarlyHooks();
        }
        // Maybe uninstall (and stop here).

        if ($this->Config->§uninstall) {
            $this->s::maybeUninstall();
            return; // Stop here.
        }
        // Maybe install (or reinstall).

        $this->s::maybeInstall();

        // Make global access var available.

        $GLOBALS[$this->Config->©brand['©var']] = $this;

        // Plugin available hook.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('available', $this);
        }
        // Maybe setup other hooks.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->onSetupOtherHooks();
        }
        // Plugin setup complete hook.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('setup_complete', $this);
        }
    }

    /**
     * Early hook setup handler.
     *
     * @since 16xxxx Initial release.
     *
     * @note For extenders. Only runs when appropriate.
     */
    protected function onSetupEarlyHooks()
    {
        // Nothing in core at this time.
    }

    /**
     * Other hook setup handler.
     *
     * @since 16xxxx Initial release.
     *
     * @note For extenders. Only runs when appropriate.
     */
    protected function onSetupOtherHooks()
    {
        add_action('admin_init', [$this->Utils->§Options, 'onAdminInitMaybeSave']);
        add_action('admin_init', [$this->Utils->§Options, 'onAdminInitMaybeRestoreDefaults']);

        add_action('admin_init', [$this->Utils->§Notices, 'onAdminInitMaybeDismiss']);
        add_action('all_admin_notices', [$this->Utils->§Notices, 'onAllAdminNotices']);
    }
}
