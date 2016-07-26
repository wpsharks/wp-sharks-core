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
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrand()
     */
    public static function brandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrand(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandArg()
     */
    public static function brandUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandParent()
     */
    public static function parentBrandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandParent(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandParentArg()
     */
    public static function parentBrandUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandParentArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandCore()
     */
    public static function coreBrandUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandCore(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandCoreArg()
     */
    public static function coreBrandUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandCoreArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandApi()
     */
    public static function brandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandApi(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandApiArg()
     */
    public static function brandApiUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandApiArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandParentApi()
     */
    public static function parentBrandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandParentApi(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandParentApiArg()
     */
    public static function parentBrandApiUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandParentApiArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandCoreApi()
     */
    public static function coreBrandApiUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandCoreApi(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandCoreApiArg()
     */
    public static function coreBrandApiUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandCoreApiArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandCdn()
     */
    public static function brandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandCdn(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandCdnArg()
     */
    public static function brandCdnUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandCdnArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandParentCdn()
     */
    public static function parentBrandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandParentCdn(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandParentCdnArg()
     */
    public static function parentBrandCdnUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandParentCdnArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandCoreCdn()
     */
    public static function coreBrandCdnUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandCoreCdn(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandCoreCdnArg()
     */
    public static function coreBrandCdnUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandCoreCdnArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandStats()
     */
    public static function brandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandStats(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandStatsArg()
     */
    public static function brandStatsUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandStatsArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandParentStats()
     */
    public static function parentBrandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandParentStats(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandParentStatsArg()
     */
    public static function parentBrandStatsUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandParentStatsArg(...$args);
    }

    /**
     * @since 160625 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::toBrandCoreStats()
     */
    public static function coreBrandStatsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->toBrandCoreStats(...$args);
    }

    /**
     * @since 160629 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\BrandUrl::brandCoreStatsArg()
     */
    public static function coreBrandStatsUrlArg(...$args)
    {
        return $GLOBALS[static::class]->Utils->§BrandUrl->brandCoreStatsArg(...$args);
    }
}
