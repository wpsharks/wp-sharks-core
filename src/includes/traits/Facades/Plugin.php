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

trait Plugin
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugin::slug()
     */
    public static function pluginSlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->slug(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugin::slugs()
     */
    public static function pluginSlugs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->slugs(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugin::isInstalled()
     */
    public static function pluginIsInstalled(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->isInstalled(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugin::installedData()
     */
    public static function installedPluginData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugin->installedData(...$args);
    }
}
