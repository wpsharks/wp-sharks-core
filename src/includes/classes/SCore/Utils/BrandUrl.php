<?php
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
 * Brand URL.
 *
 * @since 160524 URL to post ID.
 */
class BrandUrl extends Classes\SCore\Base\Core
{
    /**
     * URL to brand domain/path.
     *
     * @since 160524 URL to brand domain/path.
     *
     * @param string $relative_uri URI relative to brand domain/path.
     *
     * @return string Full URL leading to brand domain/path/[uri].
     */
    public function __invoke(string $relative_uri = ''): string
    {
        if (!$this->App->Config->©brand['§domain']) {
            throw $this->c::issue('Missing brand domain.');
        }
        $url = 'https://'.$this->App->Config->©brand['§domain'];
        $url .= $this->App->Config->©brand['§domain_path'];

        if ($relative_uri) {
            $url = $this->c::mbRTrim($url, '/');
            $url .= '/'.$this->c::mbLTrim($relative_uri, '/');
        }
        return $url;
    }
}
