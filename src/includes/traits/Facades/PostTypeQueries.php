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

trait PostTypeQueries
{
    /**
     * @since 160525 App.
     */
    public static function postTypesQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->total(...$args);
    }

    /**
     * @since 160525 App.
     */
    public static function postTypesQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->all(...$args);
    }

    /**
     * @since 160525 Initial release.
     */
    public static function postTypeSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostTypesQuery->selectOptions(...$args);
    }
}
