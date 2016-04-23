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

trait PostQueries
{
    /**
     * @since 16xxxx App.
     */
    public static function postsQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostQuery->total(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function postsQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostQuery->all(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function postSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostQuery->selectOptions(...$args);
    }
}
