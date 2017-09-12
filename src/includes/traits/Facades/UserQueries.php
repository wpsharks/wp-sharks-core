<?php
/**
 * User queries.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * User queries.
 *
 * @since 160524
 */
trait UserQueries
{
    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\UsersQuery::total()
     */
    public static function usersQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§UsersQuery->total(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\UsersQuery::all()
     */
    public static function usersQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§UsersQuery->all(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\UsersQuery::selectOptions()
     */
    public static function userSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§UsersQuery->selectOptions(...$args);
    }
}
