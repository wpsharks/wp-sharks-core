<?php
/**
 * Application.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes;

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
#
use WebSharks\WpSharks\Core\Classes\SCore\Base\Wp;
use WebSharks\WpSharks\Core\Classes\SCore\Base\WpAppUrl;
use WebSharks\WpSharks\Core\Classes\SCore\Base\WpAppKeys;

/**
 * App (plugins must extend).
 *
 * @since 160524 Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * WP common.
     *
     * @since 160524
     *
     * @type Wp
     */
    public $Wp;

    /**
     * Version.
     *
     * @since 160524
     *
     * @type string Version.
     */
    const VERSION = '170329.62005'; //v//

    /**
     * ReST action API version.
     *
     * @since 160625 ReST actions.
     *
     * @type string API version.
     */
    const REST_ACTION_API_VERSION = '1.0';

    /**
     * Core container slug.
     *
     * @since 160702 Core container.
     *
     * @type string Core container slug.
     */
    const CORE_CONTAINER_SLUG = 'wp-sharks';

    /**
     * Core container var.
     *
     * @since 160702 Core container.
     *
     * @type string Core container var.
     */
    const CORE_CONTAINER_VAR = 'wp_sharks';

    /**
     * Core container name.
     *
     * @since 160702 Core container.
     *
     * @type string Core container name.
     */
    const CORE_CONTAINER_NAME = 'WP Sharks';

    /**
     * Core container domain.
     *
     * @since 160713 Core container.
     *
     * @type string Core container domain.
     */
    const CORE_CONTAINER_DOMAIN = 'wpsharks.com';

    /**
     * Core license key.
     *
     * @since 160715 Core container.
     *
     * @type string Core license key.
     */
    const CORE_LICENSE_KEY = 'WP-SHARKS-CORE-XXXX-XXXX-XXXX-XXXX-XXXX';

    /**
     * Constructor.
     *
     * @since 160524 Initial release.
     *
     * @param array            $instance_base Instance base.
     * @param array            $instance      Instance args.
     * @param Classes\App|null $Parent        Parent app (optional).
     */
    public function __construct(array $instance_base = [], array $instance = [], Classes\App $Parent = null)
    {
        # WordPress common properties.

        $this->Wp = $Parent ? $Parent->Wp : new Wp();

        # Define a few reflection-based properties.

        $this->Reflection = new \ReflectionClass($this);

        $this->class     = $this->Reflection->getName();
        $this->namespace = $this->Reflection->getNamespaceName();

        $this->file              = $this->Reflection->getFileName();
        $this->base_dir          = dirname($this->file, 4);
        $this->base_dir_basename = basename($this->base_dir);

        $this->is_core = $this->class === self::class;

        # Establish specs & brand for parent constructor.

        $default_specs = [
            '§is_pro'          => null,
            '§has_pro'         => null,
            '§is_elite'        => null,
            '§has_elite'       => null,
            '§in_wp'           => null,
            '§is_network_wide' => null,
            '§type'            => '',
            '§file'            => '',
        ];
        $brand_defaults = [
            '©acronym' => '',
            '©name'    => '',

            '©slug' => '',
            '©var'  => '',

            '©short_slug' => '',
            '©short_var'  => '',

            '§product_name' => '',
            '§product_slug' => '',

            '©text_domain' => '',

            '§domain'           => '',
            '§domain_path'      => '',
            '§domain_pro_path'  => '',
            '§domain_short_var' => '',

            '§api_domain'           => '',
            '§api_domain_path'      => '',
            '§api_domain_short_var' => '',

            '§cdn_domain'           => '',
            '§cdn_domain_path'      => '',
            '§cdn_domain_short_var' => '',

            '§stats_domain'           => '',
            '§stats_domain_path'      => '',
            '§stats_domain_short_var' => '',
        ];
        if ($this->is_core) {
            $Parent = null; // Core.

            $specs = array_merge(
                $default_specs,
                [
                    '§is_pro'          => false,
                    '§has_pro'         => false,
                    '§is_elite'        => false,
                    '§has_elite'       => false,
                    '§in_wp'           => false,
                    '§is_network_wide' => false,
                    '§type'            => 'plugin',
                    '§file'            => $this->base_dir.'/plugin.php',
                ],
                $instance_base['§specs'] ?? [],
                $instance['§specs'] ?? []
            );
            $specs['§is_network_wide'] = $specs['§is_network_wide'] && $this->Wp->is_multisite;

            $brand = array_merge(
                $brand_defaults,
                [
                    '©acronym' => 'WPS Core',
                    '©name'    => 'WP Sharks Core',

                    '©slug' => 'wp-sharks-core',
                    '©var'  => 'wp_sharks_core',

                    '©short_slug' => 'wps-core',
                    '©short_var'  => 'wps_core',

                    '§product_name' => 'WP Sharks Core',
                    '§product_slug' => 'wp-sharks-core',

                    '©text_domain' => 'wp-sharks-core',

                    '§domain'            => 'wpsharks.com',
                    '§domain_path'       => '/product/wp-sharks-core',
                    '§domain_pro_path'   => '', // Not applicable.
                    '§domain_elite_path' => '', // Not applicable.
                    '§domain_short_var'  => 'wps',

                    '§api_domain'           => 'api.wpsharks.com',
                    '§api_domain_path'      => '/',
                    '§api_domain_short_var' => 'wps',

                    '§cdn_domain'           => 'cdn.wpsharks.com',
                    '§cdn_domain_path'      => '/',
                    '§cdn_domain_short_var' => 'wps',

                    '§stats_domain'           => 'stats.wpsharks.io',
                    '§stats_domain_path'      => '/',
                    '§stats_domain_short_var' => 'wps',
                ],
                $instance_base['©brand'] ?? [],
                $instance['©brand'] ?? []
            );
        } else {
            if (!isset($GLOBALS[self::class])) {
                throw new Exception('Missing core instance.');
            }
            $Parent = $Parent ?? $GLOBALS[self::class];

            $specs                     = array_merge($default_specs, $instance_base['§specs'] ?? [], $instance['§specs'] ?? []);
            $specs['§is_pro']          = $specs['§is_pro'] ?? mb_stripos($this->namespace, '\\Pro\\') !== false;
            $specs['§has_pro']         = $specs['§has_pro'] ?? ($specs['§is_pro'] ?: true); // Assume true.
            $specs['§is_elite']        = $specs['§is_elite'] ?? mb_stripos($this->namespace, '\\Elite\\') !== false;
            $specs['§has_elite']       = $specs['§has_elite'] ?? ($specs['§is_elite'] ?: false); // Assume false.
            $specs['§in_wp']           = $specs['§is_pro'] || $specs['§is_elite'] ? false : ($specs['§in_wp'] ?? false);
            $specs['§is_network_wide'] = $specs['§is_network_wide'] && $this->Wp->is_multisite;

            if (!$specs['§type'] || !$specs['§file']) {
                if (is_file($this->base_dir.'/plugin.php')) {
                    $specs['§type'] = 'plugin';
                    $specs['§file'] = $this->base_dir.'/plugin.php';
                } elseif (is_file($this->base_dir.'/style.css')) {
                    $specs['§type'] = 'theme';
                    $specs['§file'] = $this->base_dir.'/style.css';
                } elseif (is_file($this->base_dir.'/src/wp-content/mu-plugins/site.php')) {
                    $specs['§type'] = 'mu-plugin';
                    $specs['§file'] = $this->base_dir.'/src/wp-content/mu-plugins/site.php';
                } else { // Hard failure in this unexpected case.
                    throw new Exception('Unable to determine `§type`/`§file`.');
                } // The app will need to give its §type/§file explicitly.
            }
            $brand = array_merge($brand_defaults, $instance_base['©brand'] ?? [], $instance['©brand'] ?? []);

            if (!$brand['©slug']) { // This is the basis for others.
                $brand['©slug'] = $Parent->c::nameToSlug($this->base_dir_basename);
                $brand['©slug'] = preg_replace('/[_\-]+(?:lite|pro)/ui', '', $brand['©slug']);
            }
            $brand['©var'] = $brand['©var'] ?: $Parent->c::slugToVar($brand['©slug']);

            $brand['©name']    = $brand['©name'] ?: $Parent->c::slugToName($brand['©slug']);
            $brand['©acronym'] = $brand['©acronym'] ?: $Parent->c::nameToAcronym($brand['©name']);

            $brand['©short_slug'] = $brand['©short_slug'] ?: (strlen($brand['©slug']) <= 10 ? $brand['©slug'] : 's'.substr(md5($brand['©slug']), 0, 9));
            $brand['©short_var']  = $brand['©short_var'] ?: $Parent->c::slugToVar($brand['©short_slug']);

            $brand['§product_name'] = $brand['§product_name'] ?: $brand['©name'].($specs['§is_pro'] ? ' Pro' : ($specs['§is_elite'] ? ' Elite' : ''));
            $brand['§product_slug'] = $brand['§product_slug'] ?: $this->base_dir_basename;

            $brand['©text_domain'] = $brand['©text_domain'] ?: $brand['©slug'];

            if (!$brand['§domain'] || !$brand['§domain_path'] || !$brand['§domain_short_var']) {
                $brand['§domain']      = $Parent->Config->©brand['§domain'];
                $brand['§domain_path'] = '/product/'.$brand['§product_slug'];

                $brand['§domain_pro_path'] = $specs['§is_pro'] ? $brand['§domain_path']
                    : ($specs['§has_pro'] ? '/product/'.$brand['§product_slug'].'-pro' : '');

                $brand['§domain_elite_path'] = $specs['§is_elite'] ? $brand['§domain_path']
                    : ($specs['§has_elite'] ? '/product/'.$brand['§product_slug'].'-elite' : '');

                $brand['§domain_short_var'] = $Parent->Config->©brand['§domain_short_var'];
            }
            if ($this->Wp->debug) {
                if (preg_match('/(?:LITE|PRO|ELITE)$/ui', $brand['©acronym'])) {
                    throw new Exception('Please remove `LITE|PRO|ELITE` suffix from `©acronym`.');
                } elseif (preg_match('/\s+(?:Lite|Pro|Elite)$/ui', $brand['©name'])) {
                    throw new Exception('Please remove `Lite|Pro|Elite` suffix from `©name`.');
                    //
                } elseif (!$Parent->c::isSlug($brand['©slug'])) {
                    throw new Exception('Please fix; `©slug` has invalid chars.');
                } elseif (preg_match('/[_\-]+(?:lite|pro|elite)$/ui', $brand['©slug'])) {
                    throw new Exception('Please remove `lite|pro|elite` suffix from `©slug`.');
                    //
                } elseif (!$Parent->c::isVar($brand['©var'])) {
                    throw new Exception('Please fix; `©var` has invalid chars.');
                } elseif (preg_match('/[_\-]+(?:lite|pro|elite)$/ui', $brand['©var'])) {
                    throw new Exception('Please remove `lite|pro|elite` suffix from `©var`.');
                    //
                } elseif (strlen($brand['©short_slug']) > 10) {
                    throw new Exception('Please fix; `©short_slug` is > 10 bytes.');
                } elseif (!$Parent->c::isSlug($brand['©short_slug'])) {
                    throw new Exception('Please fix; `©short_slug` has invalid chars.');
                } elseif (preg_match('/[_\-]+(?:lite|pro|elite)$/ui', $brand['©short_slug'])) {
                    throw new Exception('Please remove `lite|pro|elite` suffix from `©short_slug`.');
                    //
                } elseif (strlen($brand['©short_var']) > 10) {
                    throw new Exception('Please fix; `©short_var` is > 10 bytes.');
                } elseif (!$Parent->c::isVar($brand['©short_var'])) {
                    throw new Exception('Please fix; `©short_var` has invalid chars.');
                } elseif (preg_match('/[_\-]+(?:lite|pro|elite)$/ui', $brand['©short_var'])) {
                    throw new Exception('Please remove `lite|pro|elite` suffix from `©short_var`.');
                    //
                } elseif (!$Parent->c::isSlug($brand['©text_domain'])) {
                    throw new Exception('Please fix; `©text_domain` has invalid chars.');
                    //
                } elseif (!$Parent->c::isSlug($brand['§product_slug'])) {
                    throw new Exception('Please fix; `§product_slug` has invalid chars.');
                }
            }
        }
        # Acquire encryption & salt keys.
        # Collect app URL parts, based on app type.

        $Url  = new WpAppUrl($this->Wp, $specs, $brand);
        $Keys = new WpAppKeys($this->Wp, $specs, $brand);

        # Build the core/default instance base.

        $default_instance_base = [
            '©use_server_cfgs' => false,

            '©debug' => [
                '©enable' => $this->Wp->debug,
                '©edge'   => $this->Wp->debug_edge,

                '©log'          => $this->Wp->debug_log,
                '©log_callback' => false, // For extenders.

                '©er_enable'     => false, // WP handles this.
                '©er_display'    => false, // WP handles this.
                '©er_assertions' => false, // Developer must enable.
                // WordPress itself may handle assertions in the future.
            ],
            '©handle_throwables' => false, // Never in a shared codespace.

            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                        Classes\SCore\Base\Widget::class,
                        Classes\SCore\Base\WpAppUrl::class,
                        Classes\SCore\Base\WpAppKeys::class,

                        Classes\SCore\MenuPageForm::class,
                        Classes\SCore\PostMetaBoxForm::class,
                        Classes\SCore\WidgetForm::class,
                    ],
                ],
            ],

            '©sub_namespace_map' => [
                'SCore' => [
                    '©utils'   => '§',
                    '©facades' => 's',
                ],
            ],

            '§specs' => $default_specs, // Established already.
            '©brand' => $brand_defaults, // Established already.

            '©urls' => [
                '©hosts' => [
                    '©app' => $this->Wp->site_url_host,
                    '©cdn' => 'cdn.'.$this->Wp->site_url_root_host,

                    '©roots' => [
                        '©app' => $this->Wp->site_url_root_host,
                        '©cdn' => $this->Wp->site_url_root_host,
                    ],
                ],
                '©base_paths' => [
                    '©app' => $Url->base_path,
                    '©cdn' => '/',
                ],
                '©cdn_filter_enable' => false,
                '©default_scheme'    => $this->Wp->site_default_scheme,
                '©sig_key'           => $Keys->salt_key,
            ],

            '§setup' => [ // On (or after) `plugins_loaded`.
                '§hook'          => 'after_setup_theme',
                '§hook_priority' => 0, // Very early.

                // Other setup flags.
                '§enable_hooks' => true,

                // Systematic setup flags.
                '§complete' => false,
            ],

            '©fs_paths' => [
                '©logs_dir'                 => $this->Wp->tmp_dir.'/.'.$this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'].'/logs',
                '©cache_dir'                => $this->Wp->tmp_dir.'/.'.$this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'].'/cache',
                '©sris_dir'                 => $this->base_dir.'/src/client-s', // Store SRIs locally.
                '§templates_theme_base_dir' => $this::CORE_CONTAINER_SLUG.'/'.$brand['©slug'],
                '©templates_dir'            => $this->base_dir.'/src/includes/templates',
                '©routes_dir'               => '', // Not applicable.
                '©errors_dir'               => '', // Not applicable.

                '§mysql' => [
                    '§tables_dir'   => $this->base_dir.'/src/includes/mysql/tables',
                    '§indexes_dir'  => $this->base_dir.'/src/includes/mysql/indexes',
                    '§triggers_dir' => $this->base_dir.'/src/includes/mysql/triggers',
                ],
            ],

            '©encryption' => [
                '©key' => $Keys->encryption_key,
            ],
            '©cookies' => [
                '©encryption_key' => $Keys->encryption_key,
            ],
            '©hash' => [
                '©key' => $Keys->salt_key,
            ],
            '©hash_ids' => [
                '©hash_key' => $Keys->salt_key,
            ],
            '©passwords' => [
                '©hash_key' => $Keys->salt_key,
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
                            A successful test must return nothing.
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
                            A successful test must return nothing.
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
                            A successful test must return nothing.
                            A failed test must return an array with:
                                - `how_to_resolve` = Brief rich-text description; i.e., → To resolve, [how_to_resolve].
                                - `cap_to_resolve` = Cap required to satisfy; e.g., `manage_options`.
                        ],
                    */
                ],
            ],

            '§caps' => [
                '§manage' => $specs['§is_network_wide'] && $this->Wp->is_multisite
                    ? 'manage_network_plugins' : 'activate_plugins',
            ],

            '§pro_option_keys'   => [],
            '§elite_option_keys' => [],
            '§default_options'   => [
                '§for_version'      => $this::VERSION,
                '§for_product_slug' => $brand['§product_slug'],
                '§license_key'      => '', // For product slug.
            ],
            '§options' => [], // Filled automatically (see below).

            '§force_install' => false, // Force install (or reinstall)?
            '§uninstall'     => false, // Uninstall? e.g., on deletion.
        ];
        # Automatically add lite/pro/elite conflicts to the array.

        if ($specs['§type'] === 'plugin') { // Only for plugins; n/a to themes.
            if ($specs['§is_pro']) {
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug']]               = $brand['©name'];
                $default_instance_base['§conflicts']['§deactivatable_plugins'][$brand['©slug']] = $brand['©name'];
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug'].'-elite']      = $brand['©name'].' Elite';
            } elseif ($specs['§is_elite']) {
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug']]                      = $brand['©name'];
                $default_instance_base['§conflicts']['§deactivatable_plugins'][$brand['©slug']]        = $brand['©name'];
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug'].'-pro']               = $brand['©name'].' Pro';
                $default_instance_base['§conflicts']['§deactivatable_plugins'][$brand['©slug'].'-pro'] = $brand['©name'].' Pro';
            } else {
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug'].'-pro']   = $brand['©name'].' Pro';
                $default_instance_base['§conflicts']['§plugins'][$brand['©slug'].'-elite'] = $brand['©name'].' Elite';
            }
        }
        # Merge `$default_instance_base` w/ `$instance_base` param.

        $instance_base           = $this->mergeConfig($default_instance_base, $instance_base);
        $instance_base['§specs'] = &$specs; // Already established (in full) above.
        $instance_base['©brand'] = &$brand; // Already established (in full) above.

        # Give plugins/extensions a chance to filter `$instance`.

        $instance = apply_filters($brand['©var'].'_instance', $instance, $instance_base);
        unset($instance['§specs'], $instance['©brand']); // Already established (in full) above.

        # Call parent app-constructor (i.e., websharks/core).

        parent::__construct($instance_base, $instance, $Parent);

        # Post-construct sub-routines.

        $this->prepareOptions();
        $this->transitionOptions();
        $this->initialize();
    }

    /**
     * Prepare options.
     *
     * @since 170311.43193 Prep options.
     */
    protected function prepareOptions()
    {
        if ($this->Config->§specs['§is_network_wide'] && $this->Wp->is_multisite) {
            if (!is_array($site_owner_options = get_network_option(null, $this->Config->©brand['©var'].'_options'))) {
                update_network_option(null, $this->Config->©brand['©var'].'_options', $site_owner_options = []);
            }
        } elseif (!is_array($site_owner_options = get_option($this->Config->©brand['©var'].'_options'))) {
            update_option($this->Config->©brand['©var'].'_options', $site_owner_options = []);
        }
        $this->Config->§options = $this->s::mergeOptions($this->Config->§default_options, $this->Config->§options);
        $this->Config->§options = $this->s::mergeOptions($this->Config->§options, $site_owner_options);
        $this->Config->§options = $this->s::applyFilters('options', $this->Config->§options);
    }

    /**
     * Maybe transition options.
     *
     * @since 170311.43193 Option transitions.
     */
    protected function transitionOptions()
    {
        if ($this->Config->§options['§for_product_slug'] !== $this->Config->©brand['§product_slug']) {
            $this->Config->§options['§for_product_slug'] = $this->Config->©brand['§product_slug'];
            $this->Config->§options['§license_key']      = ''; // No longer applicable.

            if ($this->Config->§specs['§is_network_wide'] && $this->Wp->is_multisite) {
                update_network_option(null, $this->Config->©brand['©var'].'_options', $this->Config->§options);
            } else {
                update_option($this->Config->©brand['©var'].'_options', $this->Config->§options);
            }
            $this->Config->§force_install = !$this->Config->§uninstall; // Force reinstall (if not uninstalling).
        }
    }

    /**
     * Initialize.
     *
     * @since 170311.43193 Inits.
     */
    protected function initialize()
    {
        // Sanity check; must be on (or after) `plugins_loaded` hook.
        // If uninstalling, must be on (or after) `init` hook.

        if (!did_action('plugins_loaded')) {
            throw new Exception('`plugins_loaded` action not done yet.');
        } elseif ($this->Config->§uninstall && !did_action('init')) {
            throw new Exception('`init` action not done yet.');
        }
        // Check for any known conflicts.

        if ($this->s::conflictsExist()) {
            return; // Stop here.
        }
        // Check for any unsatisfied dependencies.

        if ($this->s::dependenciesUnsatisfied()) {
            return; // Stop here.
        }
        // Add app instance to collection.

        if ($this->Parent && $this->Parent->is_core && !$this->Config->§uninstall) {
            $this->Parent->s::addApp($this);
        }
        // Remaining routines are driven by setup hook.

        if ($this->Config->§uninstall || did_action($this->Config->§setup['§hook'])) {
            $this->onSetup(); // Run setup immediately.
        } else { // Delay setup routines; i.e., attach to hook.
            add_action($this->Config->§setup['§hook'], [$this, 'onSetup'], $this->Config->§setup['§hook_priority']);
        }
    }

    /**
     * Run setup routines.
     *
     * @since 160524 Initial release.
     */
    public function onSetup()
    {
        if ($this->Config->§setup['§complete']) {
            return; // Already complete.
        }
        $this->Config->§setup['§complete'] = true;

        // Maybe setup early hooks.
        // e.g., Install/uninstall hooks.

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

        // Maybe expire a trial period.

        $is_trial_expired = false; // Initialize.

        if (!$this->is_core // Check if software trial is expired.
            && ($this->Config->§specs['§is_pro'] || $this->Config->§specs['§is_elite'])
            && !$this->Config->§options['§license_key']) {
            $is_trial_expired = $this->s::maybeExpireTrial();
        }
        // Make global access var available.

        $GLOBALS[$this->Config->©brand['©var']] = $this;

        if ($this->Config->§specs['§type'] === 'theme') {
            $GLOBALS[$this::CORE_CONTAINER_VAR.'_theme'] = $this;
        }
        // Plugin available hook.
        // i.e., Global is available.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('available', $this);
        }
        // Maybe setup other hooks.
        // i.e., Functionality hooks/filters.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->onSetupCoreHooks();
        }
        if ($this->Config->§setup['§enable_hooks'] && !$is_trial_expired) {
            $this->onSetupOtherHooks();
        }
        // Setup fully complete hook.
        // e.g., For theme/plugin add-ons.

        if ($this->Config->§setup['§enable_hooks']) {
            $this->s::doAction('setup_complete', $this);
        }
    }

    /**
     * Early hook setup handler.
     *
     * @since 160524 Initial release.
     *
     * @internal Only runs when appropriate.
     */
    protected function onSetupEarlyHooks()
    {
        // Nothing in core at this time.
    }

    /**
     * Core hook setup handler.
     *
     * @since 161014 Initial release.
     *
     * @internal Only runs when appropriate.
     */
    protected function onSetupCoreHooks()
    {
        $is_theme  = $this->Config->§specs['§type'] === 'theme';
        $is_plugin = $this->Config->§specs['§type'] === 'plugin';

        add_action('wp_loaded', [$this->Utils->§RestAction, 'onWpLoaded']);
        add_action('wp_loaded', [$this->Utils->§TransientShortlink, 'onWpLoaded']);

        if ($this->Wp->is_admin) {
            add_action('admin_init', [$this->Utils->§AppStats, 'onAdminInit']);
            add_action('admin_init', [$this->Utils->§Updater, 'onAdminInit']);

            if ($this->is_core) {
                add_action('network_admin_menu', [$this->Utils->{'§CoreOnly\\MenuPages'}, 'onNetworkAdminMenu']);
                add_action('admin_menu', [$this->Utils->{'§CoreOnly\\MenuPages'}, 'onAdminMenu']);
                add_action('admin_enqueue_scripts', [$this->Utils->§StylesScripts, 'enqueueMenuPageLibs']);
            }
            add_filter('admin_body_class', [$this->Utils->§MenuPage, 'onAdminBodyClass']);
            add_action('all_admin_notices', [$this->Utils->§Notices, 'onAllAdminNotices']);

            if ($is_plugin) { // This is specifically for a plugin, and only for a plugin.
                add_filter('plugin_action_links_'.plugin_basename($this->Config->§specs['§file']), [$this->Utils->§MenuPage, 'onPluginActionLinks']);
            }
        }
        if ($is_theme || $is_plugin) { // This attaches in all contexts for compatibility w/ third-party update tools; e.g., ManageWP.
            add_filter(// This is so WordPress will know about available updates for our products; i.e., those not listed @ WordPress.org.
                'site_transient_update_'.$this->Config->§specs['§type'].'s', // i.e., a `get_site_transient()` filter.
                [$this->Utils->§Updater, 'onGetSiteTransientUpdate'.$this->Config->§specs['§type'].'s']
            );
        }
        if ($this->is_core) { // Deals with OPcache resets in core.
            add_action('upgrader_process_complete', [$this->Utils->§Updater, 'onUpgraderProcessComplete']);
        }
    }

    /**
     * Other hook setup handler.
     *
     * @since 160524 Initial release.
     *
     * @internal Only runs when appropriate.
     */
    protected function onSetupOtherHooks()
    {
        // Nothing in core at this time.
    }
}
