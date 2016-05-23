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

trait Plugins
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function activePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->active(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function networkActivePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->networkActive(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function allActivePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->allActive(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function allInstalledPlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->allInstalled(...$args);
    }
}
