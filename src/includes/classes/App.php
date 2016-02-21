<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils;
use WebSharks\WpSharks\Core\Functions as wc;
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
 * Application.
 *
 * @since 16xxxx Initial release.
 */
class App extends CoreClasses\App
{
    /**
     * File.
     *
     * @since 16xxxx
     *
     * @type string
     */
    public $file;

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
     */
    public function __construct()
    {
        $brand = [
            'slug'        => 'wp-sharks-core',
            'var'         => 'wp_sharks_core',
            'name'        => 'WP Sharks Core',
            'acronym'     => 'WPSC',
            'prefix'      => 'wpsc',
            'domain'      => 'wpsharks.com',
            'domain_path' => '/product/core',
            'text_domain' => 'wp-sharks-core',
        ];
        $site_salt    = str_pad(wp_salt(), 64, 'x');
        $site_tmp_dir = rtrim(get_temp_dir(), '/').'/'.sha1(ABSPATH);
        $site_scheme  = mb_strtlower(parse_url(site_url('/'), PHP_URL_SCHEME));

        $this->file = dirname(__FILE__, 4).'/plugin.php';

        parent::__construct([
            'debug'             => false,
            'handle_exceptions' => false,

            'contacts' => [
                'admin' => [
                    'name'         => '',
                    'email'        => '',
                    'public_email' => '',
                ],
            ],

            'di' => [
                'default_rule' => [
                    'new_instances' => [
                        Plugin::class,
                        PluginDi::class,
                        PluginConfig::class,
                        PluginUtils::class,
                    ],
                ],
            ],

            'mysql_db' => [
                'hosts'  => [],
                'shards' => [],
            ],

            'brand' => [
                'slug'    => $brand['slug'],
                'var'     => $brand['var'],
                'name'    => $brand['name'],
                'acronym' => $brand['acronym'],
                'prefix'  => $brand['prefix'],

                'keywords'    => '',
                'description' => '',
                'tagline'     => '',

                'favicon'    => '',
                'logo'       => '',
                'screenshot' => '',
            ],

            'urls' => [
                'hosts' => [
                    'roots' => [
                        'app' => '',
                    ],
                    'app'    => '',
                    'cdn'    => '',
                    'cdn_s3' => '',
                ],
                'cdn_filter_enable' => false,
                'default_scheme'    => $site_scheme,
                'sig_key'           => $site_salt,
            ],

            'fs_paths' => [
                'tmp_dir'       => $site_tmp_dir.'/'.$brand['slug'].'/tmp',
                'logs_dir'      => $site_tmp_dir.'/'.$brand['slug'].'/log',
                'cache_dir'     => $site_tmp_dir.'/'.$brand['slug'].'/cache',
                'templates_dir' => dirname(__FILE__, 2).'/src/includes/templates',
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
                'text_domain' => $brand['text_domain'],
            ],

            'email' => [
                'from_name'  => '',
                'from_email' => '',

                'reply_to_name'  => '',
                'reply_to_email' => '',

                'smtp_host'   => '',
                'smtp_port'   => 0,
                'smtp_secure' => '',

                'smtp_username' => '',
                'smtp_password' => '',
            ],

            'cookies' => [
                'encryption_key' => $site_salt,
            ],
            'hash_ids' => [
                'hash_key' => $site_salt,
            ],
            'passwords' => [
                'hash_key' => $site_salt,
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

            'app' => [
                'brand' => $brand,
                'keys'  => [
                    'salt' => $site_salt,
                ],
            ],
        ]);
    }
}
