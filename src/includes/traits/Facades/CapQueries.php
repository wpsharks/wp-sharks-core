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

trait CapQueries
{
    /**
     * @since 16xxxx App.
     */
    public static function capsQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->total(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function capsQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->all(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function capSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->selectOptions(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function capsForRole(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->forRole(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function capsCollectAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->collectAll(...$args);
    }
}
