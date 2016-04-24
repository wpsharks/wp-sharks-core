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

trait RoleQueries
{
    /**
     * @since 16xxxx App.
     */
    public static function rolesQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->total(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function rolesQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->all(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function roleSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RolesQuery->selectOptions(...$args);
    }
}
