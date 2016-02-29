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

trait Notices
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function enqueueNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->enqueue(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function enqueueUserNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->userEnqueue(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function dismissNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Notices->dismiss(...$args);
    }
}