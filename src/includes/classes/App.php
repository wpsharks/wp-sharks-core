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
 * Application.
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
    const VERSION = '160217'; //v//

    /**
     * Constructor.
     *
     * @since 16xxxx Initial release.
     */
    public function __construct()
    {
        $blog_salt    = wp_salt();
        $blog_tmp_dir = rtrim(get_temp_dir(), '/');
        $blog_scheme  = mb_strtlower(parse_url(site_url('/'), PHP_URL_SCHEME));

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

                    ],
                ],
            ],

            'mysql_db' => [
                'hosts'  => [],
                'shards' => [],
            ],

            'brand' => [
                'acronym' => '',
                'slug'    => '',
                'name'    => '',

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
                'default_scheme'    => $blog_scheme,
                'sig_key'           => $blog_salt,
            ],

            'fs_paths' => [
                'logs_dir'      => $blog_tmp_dir.'/wp-sharks-core/log',
                'cache_dir'     => $blog_tmp_dir.'/wp-sharks-core/cache',
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
                'text_domain' => 'wp-sharks-core',
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
        ]);
    }
}
