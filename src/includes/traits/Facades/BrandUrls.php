<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

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

trait BrandUrls
{
    /**
     * @since 160524 Initial release.
     */
    public static function brandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrand(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function parentBrandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandParent(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function coreBrandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandCore(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function brandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandApi(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function parentBrandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandParentApi(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function coreBrandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandCoreApi(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function brandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandCdn(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function parentBrandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandParentCdn(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function coreBrandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandCoreCdn(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function brandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandStats(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function parentBrandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandParentStats(...$args);
    }

    /**
     * @since 160625 Initial release.
     */
    public static function coreBrandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrls->toBrandCoreStats(...$args);
    }
}
