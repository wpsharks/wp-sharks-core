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
 * Brand URLs.
 *
 * @since 160625 Brand URLs.
 */
class BrandUrls extends Classes\SCore\Base\Core
{
    /**
     * URL to brand.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrand(string $uri = ''): string
    {
        if (!($host = $this->App->Config->©brand['§domain'])) {
            throw $this->c::issue('Missing brand domain.');
        }
        $uri = $uri ? $this->c::mbLTrim($uri, '/') : '';
        $uri = $uri ? '/'.$uri : ''; // Force leading slash.

        $base_path = $this->App->Config->©brand['§domain_path'];
        $base_path = $base_path && $uri ? $this->c::mbRTrim($base_path, '/') : $base_path;

        return $url = 'https://'.$host.$base_path.$uri;
    }

    /**
     * URL to parent brand.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandParent(string $uri = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrls->toBrand($uri) : $this->toBrand($uri);
    }

    /**
     * URL to core brand.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandCore(string $uri = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrls->toBrandCore($uri);
        }
        return $this->toBrand($uri);
    }

    /**
     * URL to brand API.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandApi(string $uri = ''): string
    {
        if (!($host = $this->App->Config->©brand['§api_domain'])) {
            throw $this->c::issue('Missing brand API domain.');
        }
        $uri = $uri ? $this->c::mbLTrim($uri, '/') : '';
        $uri = $uri ? '/'.$uri : ''; // Force leading slash.

        $base_path = $this->App->Config->©brand['§api_domain_path'];
        $base_path = $base_path && $uri ? $this->c::mbRTrim($base_path, '/') : $base_path;

        return $url = 'https://'.$host.$base_path.$uri;
    }

    /**
     * URL to parent brand API.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandParentApi(string $uri = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrls->toBrandApi($uri) : $this->toBrandApi($uri);
    }

    /**
     * URL to core brand API.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandCoreApi(string $uri = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrls->toBrandCoreApi($uri);
        }
        return $this->toBrandApi($uri);
    }

    /**
     * URL to brand CDN.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandCdn(string $uri = ''): string
    {
        if (!($host = $this->App->Config->©brand['§cdn_domain'])) {
            throw $this->c::issue('Missing brand CDN domain.');
        }
        $uri = $uri ? $this->c::mbLTrim($uri, '/') : '';
        $uri = $uri ? '/'.$uri : ''; // Force leading slash.

        $base_path = $this->App->Config->©brand['§cdn_domain_path'];
        $base_path = $base_path && $uri ? $this->c::mbRTrim($base_path, '/') : $base_path;

        return $url = 'https://'.$host.$base_path.$uri;
    }

    /**
     * URL to parent brand CDN.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandParentCdn(string $uri = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrls->toBrandCdn($uri) : $this->toBrandCdn($uri);
    }

    /**
     * URL to core brand CDN.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandCoreCdn(string $uri = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrls->toBrandCoreCdn($uri);
        }
        return $this->toBrandCdn($uri);
    }

    /**
     * URL to brand stats.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandStats(string $uri = ''): string
    {
        if (!($host = $this->App->Config->©brand['§stats_domain'])) {
            throw $this->c::issue('Missing brand stats domain.');
        }
        $uri = $uri ? $this->c::mbLTrim($uri, '/') : '';
        $uri = $uri ? '/'.$uri : ''; // Force leading slash.

        $base_path = $this->App->Config->©brand['§stats_domain_path'];
        $base_path = $base_path && $uri ? $this->c::mbRTrim($base_path, '/') : $base_path;

        return $url = 'https://'.$host.$base_path.$uri;
    }

    /**
     * URL to parent brand stats.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandParentStats(string $uri = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrls->toBrandStats($uri) : $this->toBrandStats($uri);
    }

    /**
     * URL to core brand stats.
     *
     * @since 160625 Brand URLs.
     *
     * @param string $uri URI to append.
     *
     * @return string Output URL.
     */
    public function toBrandCoreStats(string $uri = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrls->toBrandCoreStats($uri);
        }
        return $this->toBrandStats($uri);
    }
}
