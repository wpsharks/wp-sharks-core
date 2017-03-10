<?php
/**
 * Post type queries.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
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

/**
 * Post type queries.
 *
 * @since 160524
 */
trait PostTypeQueries
{
    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostTypesQuery::total()
     */
    public static function postTypesQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->total(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostTypesQuery::all()
     */
    public static function postTypesQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->all(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostTypesQuery::selectOptions()
     */
    public static function postTypeSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->selectOptions(...$args);
    }
}
