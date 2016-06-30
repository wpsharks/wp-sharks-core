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
 * Brand URL utils.
 *
 * @since 160625 Brand URLs.
 */
class BrandUrl extends Classes\SCore\Base\Core
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
     * Brand URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandArg(string $arg = ''): string
    {
        return $this->App->Config->©brand['§domain_short_var'].
            ($this->App->Config->©brand['§domain_short_var'] && $arg ? '_' : '').$arg;
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
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->toBrand($uri) : $this->toBrand($uri);
    }

    /**
     * Brand parent URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandParentArg(string $arg = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->brandArg($arg) : $this->brandArg($arg);
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
            return $this->App->Parent->Utils->§BrandUrl->toBrandCore($uri);
        }
        return $this->toBrand($uri);
    }

    /**
     * Brand core URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandCoreArg(string $arg = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrl->brandCoreArg($arg);
        }
        return $this->brandArg($arg);
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
     * Brand API URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandApiArg(string $arg = ''): string
    {
        return $this->App->Config->©brand['§api_domain_short_var'].
            ($this->App->Config->©brand['§api_domain_short_var'] && $arg ? '_' : '').$arg;
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
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->toBrandApi($uri) : $this->toBrandApi($uri);
    }

    /**
     * Brand parent API URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandParentApiArg(string $arg = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->brandApiArg($arg) : $this->brandApiArg($arg);
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
            return $this->App->Parent->Utils->§BrandUrl->toBrandCoreApi($uri);
        }
        return $this->toBrandApi($uri);
    }

    /**
     * Brand core API URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandCoreApiArg(string $arg = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrl->brandCoreApiArg($arg);
        }
        return $this->brandApiArg($arg);
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
     * Brand CDN URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandCdnArg(string $arg = ''): string
    {
        return $this->App->Config->©brand['§cdn_domain_short_var'].
            ($this->App->Config->©brand['§cdn_domain_short_var'] && $arg ? '_' : '').$arg;
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
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->toBrandCdn($uri) : $this->toBrandCdn($uri);
    }

    /**
     * Brand parent CDN URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandParentCdnArg(string $arg = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->brandCdnArg($arg) : $this->brandCdnArg($arg);
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
            return $this->App->Parent->Utils->§BrandUrl->toBrandCoreCdn($uri);
        }
        return $this->toBrandCdn($uri);
    }

    /**
     * Brand core CDN URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandCoreCdnArg(string $arg = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrl->brandCoreCdnArg($arg);
        }
        return $this->brandCdnArg($arg);
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
     * Brand stats URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandStatsArg(string $arg = ''): string
    {
        return $this->App->Config->©brand['§stats_domain_short_var'].
            ($this->App->Config->©brand['§stats_domain_short_var'] && $arg ? '_' : '').$arg;
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
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->toBrandStats($uri) : $this->toBrandStats($uri);
    }

    /**
     * Brand parent stats URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandParentStatsArg(string $arg = ''): string
    {
        return $this->App->Parent ? $this->App->Parent->Utils->§BrandUrl->brandStatsArg($arg) : $this->brandStatsArg($arg);
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
            return $this->App->Parent->Utils->§BrandUrl->toBrandCoreStats($uri);
        }
        return $this->toBrandStats($uri);
    }

    /**
     * Brand core stats URL arg.
     *
     * @since 160629 Brand URL args.
     *
     * @param string $arg Arg to create.
     *
     * @return string URL arg.
     */
    public function brandCoreStatsArg(string $arg = ''): string
    {
        if ($this->App->Parent) { // Looking for the root core.
            return $this->App->Parent->Utils->§BrandUrl->brandCoreStatsArg($arg);
        }
        return $this->brandStatsArg($arg);
    }
}
