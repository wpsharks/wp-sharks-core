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
 * Plugin config.
 *
 * @since 16xxxx Initial release.
 */
class PluginConfig extends CoreClasses\AbsCore
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
     * Plugin.
     *
     * @since 16xxxx
     *
     * @type Plugin
     */
    protected $Plugin;

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Plugin $Plugin        Instance.
     * @param array  $instance_base Instance base.
     * @param array  $instance      Instance args (highest precedence).
     */
    public function __construct(Plugin $Plugin, array $instance_base, array $instance = [])
    {
        parent::__construct();

        $this->App    = $GLOBALS[App::class];
        $this->Plugin = $Plugin;

        $default_brand = [
            'acronym' => '',

            'slug'      => '',
            'base_slug' => '',

            'var'      => '',
            'base_var' => '',

            'name'      => '',
            'base_name' => '',

            'domain'      => '',
            'text_domain' => '',

            'qv_prefix'        => '',
            'transient_prefix' => '',

            'is_pro' => null, // Pro version?
        ];
        $brand = array_merge($default_brand, $instance_base['brand'] ?? [], $instance['brand'] ?? []);

        if (!$brand['base_slug']) {
            $brand['base_slug'] = preg_replace('/\-+(?:lite|pro)$/ui', '', $brand['slug']);
        }
        if (!$brand['base_var']) {
            $brand['base_var'] = preg_replace('/_+(?:lite|pro)$/ui', '', $brand['var']);
        }
        if (!$brand['base_name']) {
            $brand['base_name'] = preg_replace('/\s+(?:lite|pro)$/ui', '', $brand['name']);
        }
        if (!isset($brand['is_pro'])) {
            $brand['is_pro'] = (bool) preg_match('/\-pro$/ui', $brand['slug']);
        }
        $blog_tmp_dir = c\mb_rtrim(get_temp_dir(), '/').'/'.sha1(ABSPATH);

        $default_instance_base = [
            'type' => 'plugin',
            'file' => '',

            'di' => [
                'default_rule' => [
                    'new_instances'    => [],
                    'constructor_args' => [
                        'Plugin' => $this->Plugin,
                    ],
                ],
            ],

            'brand' => $brand,

            'setup' => [
                'priority'     => 10,
                'enable_hooks' => true,
            ],

            'fs_paths' => [
                'tmp_dir'   => $blog_tmp_dir.'/'.$brand['base_slug'].'/tmp',
                'logs_dir'  => $blog_tmp_dir.'/'.$brand['base_slug'].'/logs',
                'cache_dir' => $blog_tmp_dir.'/'.$brand['base_slug'].'/cache',
            ],

            'keys' => [
                'salt' => c\mb_str_pad(wp_salt(), 64, 'x'),
            ],

            'caps' => [
                'administrate' => 'activate_plugins',
                'manage'       => 'activate_plugins',
                'view_notices' => 'activate_plugins',
                'recompile'    => 'activate_plugins',
                'update'       => 'update_plugins',
                'uninstall'    => 'delete_plugins',
            ],

            'conflicting' => [
                'plugins' => [ // Keys are plugin slugs, values are plugin names.
                    $brand['base_slug'].($brand['is_pro'] ? '' : '-pro') => $brand['base_name'].($brand['is_pro'] ? ' Lite' : ' Pro'),
                ],
                'deactivatble_plugins' => [ // Keys are plugin slugs, values are plugin names.
                    $brand['base_slug'].($brand['is_pro'] ? '' : '-pro') => $brand['base_name'].($brand['is_pro'] ? ' Lite' : ' Pro'),
                ],
            ],
        ];
        $instance_base['brand'] = $instance['brand'] = $brand;
        $instance_base          = $this->merge($default_instance_base, $instance_base);

        $config = $this->merge($instance_base, $instance);
        $config = apply_filters($brand['base_var'].'_config', $config);

        $this->overload((object) $config, true);
    }

    /**
     * Merge config arrays.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The resuling array after merging.
     */
    protected function merge(array $base, array $merge): array
    {
        if (isset($base['di']['default_rule']['new_instances'])) {
            $base_di_default_rule_new_instances = $base['di']['default_rule']['new_instances'];
        } // Save new instances before emptying numeric arrays.

        $base = $this->maybeEmptyNumericArrays($base, $merge);

        if (isset($base_di_default_rule_new_instances, $merge['di']['default_rule']['new_instances'])) {
            $merge['di']['default_rule']['new_instances'] = array_merge($base_di_default_rule_new_instances, $merge['di']['default_rule']['new_instances']);
        }
        return $merged = array_replace_recursive($base, $merge);
    }

    /**
     * Empty numeric arrays.
     *
     * @since 16xxxx Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The `$base` w/ possibly-empty numeric arrays.
     */
    protected function maybeEmptyNumericArrays(array $base, array $merge): array
    {
        if (!$merge) { // Save time. Merge is empty?
            return $base; // Nothing to do here.
        }
        foreach ($base as $_key => &$_value) {
            if (is_array($_value) && array_key_exists($_key, $merge)) {
                if (!$_value || $_value === array_values($_value)) {
                    $_value = []; // Empty numeric arrays.
                } elseif ($merge[$_key] && is_array($merge[$_key])) {
                    $_value = $this->maybeEmptyNumericArrays($_value, $merge[$_key]);
                }
            }
        } // unset($_key, $_value); // Housekeeping.
        return $base; // Return possibly-modified base.
    }
}
