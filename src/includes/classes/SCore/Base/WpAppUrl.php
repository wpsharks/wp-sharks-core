<?php
/**
 * WP app URL.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * WP app URL.
 *
 * @since 170311.43193 WP utils.
 */
class WpAppUrl // Stand-alone class.
{
    /**
     * Class constructor.
     *
     * @since 170311.43193 Common utils.
     */
    public function __construct(Wp $Wp, array $specs, array $brand)
    {
        if ($specs['§type'] === 'plugin') {
            if (!($this->parts = parse_url(plugin_dir_url($specs['§file'])))) {
                throw new Exception('Failed to parse plugin dir URL parts.');
            }
        } elseif ($specs['§type'] === 'theme') {
            if (!($this->parts = $Wp->template_directory_url_parts)) {
                throw new Exception('Failed to parse theme dir URL parts.');
            }
        } elseif ($specs['§type'] === 'mu-plugin') {
            if (!($this->parts = $Wp->site_url_parts)) {
                throw new Exception('Failed to parse app URL parts.');
            }
        } else { // Unexpected application `§type` in this case.
            throw new Exception('Failed to parse URL for unexpected `§type`.');
        }
        $this->base_path = rtrim($this->parts['path'] ?? '', '/');
        $this->base_path .= in_array($specs['§type'], ['theme', 'plugin'], true) ? '/src' : '';
        $this->base_path .= '/'; // Always; i.e., this is a directory location.
    }
}
