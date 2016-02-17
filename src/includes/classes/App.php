<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils;
use WebSharks\WpSharks\Core\Functions as w;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Application.
 *
 * @since 16xxxx Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * Has conflicts?
     *
     * @since 16xxxx
     *
     * @type bool|null
     */
    protected $has_conflicts;

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
        $nameToSlug = function (string $name) {
            $slug = $name; // Copy.
            $slug = trim(mb_strtolower($slug));
            $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
            $slug = trim($slug, '-');

            if ($slug && !preg_match('/^[a-z]/u', $slug)) {
                $slug = 'x'.$slug; // Force `^[a-z]`.
            }
            return $slug;
        };
        $blog_salt        = wp_salt();
        $blog_name        = get_bloginfo('name');
        $blog_admin_email = get_bloginfo('admin_email');
        $blog_description = get_bloginfo('description');
        $blog_tmp_dir     = rtrim(get_temp_dir(), '/');
        $blog_scheme      = mb_strtlower(parse_url(site_url('/'), PHP_URL_SCHEME));
        $blog_host        = mb_strtlower(parse_url(site_url('/'), PHP_URL_HOST));
        $blog_root_host   = implode('.', array_slice(explode('.', $blog_host), -2));

        $plugin_type = (string) ($instance_base['plugin']['type'] ?? '');
        $plugin_file = (string) ($instance_base['plugin']['file'] ?? '');

        $plugin_acronym = (string) ($instance_base['plugin']['acronym'] ?? '');

        $plugin_slug      = (string) ($instance_base['plugin']['slug'] ?? '');
        $plugin_base_slug = (string) ($instance_base['plugin']['base_slug'] ?? preg_replace('/\-+(?:lite|pro)$/ui', '', $plugin_slug));

        $plugin_name      = (string) ($instance_base['plugin']['name'] ?? '');
        $plugin_base_name = (string) ($instance_base['plugin']['base_name'] ?? preg_replace('/\s+(?:lite|pro)$/ui', '', $plugin_name));

        $plugin_domain      = (string) ($instance_base['plugin']['domain'] ?? '');
        $plugin_text_domain = (string) ($instance_base['plugin']['text_domain'] ?? $plugin_base_slug);

        $plugin_qv_prefix        = (string) ($instance_base['plugin']['qv_prefix'] ?? mb_strtolower($plugin_acronym).'_');
        $plugin_transient_prefix = (string) ($instance_base['plugin']['transient_prefix'] ?? $plugin_qv_prefix);

        $plugin_is_pro = (bool) ($instance_base['plugin']['is_pro'] ?? preg_match('/\-pro$/ui', $plugin_slug));

        $default_instance_base = [
            'debug'             => false, // N/A in WordPress.
            'handle_exceptions' => false, // N/A in WordPress.

            'contacts' => [
                'admin' => [
                    'name'         => $blog_name,
                    'email'        => $blog_admin_email,
                    'public_email' => $blog_admin_email,
                ],
            ],

            'di' => [
                'default_rule' => [
                    'new_instances' => [

                    ],
                ],
            ],

            'mysql_db' => [
                'hosts'  => [], // N/A; use WPDB in WordPress.
                'shards' => [], // N/A; use WPDB in WordPress.
            ],

            'brand' => [
                'acronym' => mb_strtoupper(mb_substr($blog_name, 0, 3)),
                'slug'    => $nameToSlug($blog_name),
                'name'    => $blog_name,

                'keywords'    => [$blog_host],
                'description' => $blog_description,
                'tagline'     => $blog_description,

                'favicon'    => '',
                'logo'       => '',
                'screenshot' => '',
            ],

            'urls' => [
                'hosts' => [
                    'roots' => [
                        'app' => $blog_root_host,
                    ],
                    'app'    => $blog_host,
                    'cdn'    => '',
                    'cdn_s3' => '',
                ],
                'cdn_filter_enable' => false,
                'default_scheme'    => $blog_scheme,
                'sig_key'           => $blog_salt,
            ],

            'fs_paths' => [
                'logs_dir'      => $blog_tmp_dir.'/'.$plugin_slug.'/log',
                'cache_dir'     => $blog_tmp_dir.'/'.$plugin_slug.'/cache',
                'templates_dir' => '%%app_dir%%/src/includes/templates',
                'errors_dir'    => '', // N/A in WordPress.
                'config_file'   => '', // N/A in WordPress.
            ],
            'fs_permissions' => [
                'transient_dirs' => (int) 02775,
            ],

            'memcache' => [
                'enabled'   => false,
                'namespace' => '',
                'servers'   => [],
            ],

            'i18n' => [
                'locales'     => [],
                'text_domain' => $plugin_text_domain,
            ],

            'email' => [
                'from_name'  => $blog_name,
                'from_email' => $blog_admin_email,

                'reply_to_name'  => '',
                'reply_to_email' => '',

                'smtp_host'   => '127.0.0.1',
                'smtp_port'   => 25,
                'smtp_secure' => '',

                'smtp_username' => '',
                'smtp_password' => '',
            ],

            'cookies' => [
                'encryption_key' => $blog_salt,
            ],
            'hash_ids' => [
                'hash_key' => $blog_salt,
            ],
            'passwords' => [
                'hash_key' => $blog_salt,
            ],

            'aws' => [
                'access_key' => '',
                'secret_key' => '',
            ],
            'embedly' => [
                'api_key' => '',
            ],
            'web_purify' => [
                'api_key' => '',
            ],
            'bitly' => [
                'api_key' => '',
            ],

            'plugin' => [
                'type' => $plugin_type,
                'file' => $plugin_file,

                'acronym' => $plugin_acronym,

                'slug'      => $plugin_slug,
                'base_slug' => $plugin_base_slug,

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
            ],
        ];
        parent::__construct($instance_base, $instance);
    }
}
