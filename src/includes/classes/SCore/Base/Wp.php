<?php
/**
 * WP common.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

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

/**
 * WP common.
 *
 * @since 160710 WP utils.
 */
class Wp // Stand-alone class.
{
    /**
     * Class constructor.
     *
     * @since 160710 Common utils.
     */
    public function __construct()
    {
        $this->is_multisite = is_multisite();
        $this->is_main_site = !$this->is_multisite || is_main_site();

        $this->is_admin         = is_admin();
        $this->is_user_admin    = $this->is_admin && is_user_admin();
        $this->is_network_admin = $this->is_admin && $this->is_multisite && is_network_admin();

        $this->debug         = defined('WP_DEBUG') && WP_DEBUG;
        $this->debug_edge    = $this->debug && defined('WP_DEBUG_EDGE') && WP_DEBUG_EDGE;
        $this->debug_log     = $this->debug && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG;
        $this->debug_display = $this->debug && defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY;

        if (!($this->salt = wp_salt())) {
            throw new Exception('Failed to acquire WP salt.');
        }
        if (!($this->tmp_dir = rtrim(get_temp_dir(), '/'))) {
            throw new Exception('Failed to acquire a writable tmp dir.');
        }
        if (!($this->home_url = home_url('/'))) {
            throw new Exception('Failed to acquire home URL.');
        } elseif (!($this->home_url_parts = parse_url($this->home_url))) {
            throw new Exception('Failed to parse home URL parts.');
        } elseif (!($this->home_url_host = $this->home_url_parts['host'] ?? '')) {
            throw new Exception('Failed to parse home URL host.');
        } elseif (!($this->home_url_root_host = implode('.', array_slice(explode('.', $this->home_url_host), -2)))) {
            throw new Exception('Failed to parse home URL root host.');
        }
        if (!($this->home_url_option = get_option('home'))) {
            throw new Exception('Failed to acquire home URL option.');
        } elseif (!($this->home_url_option_parts = parse_url($this->home_url_option))) {
            throw new Exception('Failed to parse home URL option parts.');
        } elseif (!($this->home_default_scheme = $this->home_url_option_parts['scheme'] ?? '')) {
            throw new Exception('Failed to parse home URL option scheme.');
        }
        if (!($this->site_url = site_url('/'))) {
            throw new Exception('Failed to acquire site URL.');
        } elseif (!($this->site_url_parts = parse_url($this->site_url))) {
            throw new Exception('Failed to parse site URL parts.');
        } elseif (!($this->site_url_host = $this->site_url_parts['host'] ?? '')) {
            throw new Exception('Failed to parse site URL host.');
        } elseif (!($this->site_url_root_host = implode('.', array_slice(explode('.', $this->site_url_host), -2)))) {
            throw new Exception('Failed to parse site URL root host.');
        }
        if (!($this->site_url_option = get_option('siteurl'))) {
            throw new Exception('Failed to acquire site URL option.');
        } elseif (!($this->site_url_option_parts = parse_url($this->site_url_option))) {
            throw new Exception('Failed to parse site URL option parts.');
        } elseif (!($this->site_default_scheme = $this->site_url_option_parts['scheme'] ?? '')) {
            throw new Exception('Failed to parse site URL option scheme.');
        }
        if (!($this->template_directory_url = get_template_directory_uri())) {
            throw new Exception('Failed to acquire template directory URL.');
        } elseif (!($this->template_directory_url_parts = parse_url($this->template_directory_url))) {
            throw new Exception('Failed to parse template directory URL parts.');
        }
        $this->template   = get_template(); // Current theme/template.
        $this->stylesheet = get_stylesheet(); // Current stylesheet.

        $this->is_woocommerce_active                 = defined('WC_VERSION') && class_exists('WooCommerce', false);
        $this->is_woocommerce_product_vendors_active = $this->is_woocommerce_active && defined('WC_PRODUCT_VENDORS_VERSION');
        $this->is_jetpack_active                     = defined('JETPACK__VERSION') && class_exists('Jetpack', false);
    }
}
