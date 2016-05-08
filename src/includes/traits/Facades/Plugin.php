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

trait Plugin
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function pluginSlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->slug(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function pluginSlugs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->slugs(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function pluginIsInstalled(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->isInstalled(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function installedPluginData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->installedData(...$args);
    }
}