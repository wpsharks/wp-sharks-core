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
 * URL to post ID.
 *
 * @since 160524 URL to post ID.
 */
class UrlToPostId extends Classes\SCore\Base\Core
{
    /**
     * URL (or URI) to post ID.
     *
     * @since 160524 URL to post ID.
     *
     * @param string $url_uri_qsl Input URL, URI, or query string w/ a leading `?`.
     *
     * @return int Post ID; oe `0` on failure.
     */
    public function __invoke(string $url_uri_qsl): int
    {
        if (!($parts = $this->c::parseUrl($url_uri_qsl))) {
            return 0; // Not possible.
        }
        if (empty($parts['scheme']) || $parts['scheme'] === '//') {
            $parts['scheme'] = $this->c::parseUrl(home_url(), PHP_URL_SCHEME);
        }
        if (empty($parts['host'])) { // Use `home_url()` host name.
            $parts['host'] = $this->c::parseUrl(home_url(), PHP_URL_HOST);
        }
        $parts['path'] = empty($parts['path']) ? '/' : $parts['path'];
        $url           = $this->c::unparseUrl($parts);

        return (int) url_to_postid($url);
    }
}
