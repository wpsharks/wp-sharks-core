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

trait CapQueries
{
    /**
     * @since 160524 App.
     */
    public static function capsQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->total(...$args);
    }

    /**
     * @since 160524 App.
     */
    public static function capsQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->all(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function capSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->selectOptions(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function capsForRole(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->forRole(...$args);
    }

    /**
     * @since 160524 Initial release.
     */
    public static function capsCollectAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§CapsQuery->collectAll(...$args);
    }
}
