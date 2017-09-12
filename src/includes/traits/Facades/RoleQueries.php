<?php
/**
 * Role queries.
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
 * Role queries.
 *
 * @since 160524
 */
trait RoleQueries
{
    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\RolesQuery::total()
     */
    public static function rolesQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->total(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\RolesQuery::all()
     */
    public static function rolesQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->all(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\RolesQuery::selectOptions()
     */
    public static function roleSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->selectOptions(...$args);
    }
}
