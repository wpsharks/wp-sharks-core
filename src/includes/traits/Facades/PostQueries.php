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

trait PostQueries
{
    /**
     * @since 160525 App.
     */
    public static function postsQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostsQuery->total(...$args);
    }

    /**
     * @since 160525 App.
     */
    public static function postsQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostsQuery->all(...$args);
    }

    /**
     * @since 160525 Initial release.
     */
    public static function postSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostsQuery->selectOptions(...$args);
    }
}
