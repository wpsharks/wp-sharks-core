<?php
/**
 * Core URL utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

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
 * Core URL utils.
 *
 * @since 160713 Core URLs.
 */
class CoreUrl extends Classes\SCore\Base\Core
{
    /**
     * URL to core container.
     *
     * @since 160713 Core URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toContainer(string $uri = ''): string
    {
        $uri = $uri ? $this->c::mbLTrim($uri, '/') : '';
        $uri = $uri ? '/'.$uri : ''; // Force leading slash.

        return $url = 'https://'.$this->App::CORE_CONTAINER_DOMAIN.$uri;
    }
}
