<?php
/**
 * Hooks.
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
 * Hooks.
 *
 * @since 160524
 */
trait Hooks
{
    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Hooks::addFilter()
     */
    public static function addFilter(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Hooks->addFilter(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Hooks::applyFilters()
     */
    public static function applyFilters(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Hooks->applyFilters(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Hooks::addAction()
     */
    public static function addAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Hooks->addAction(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Hooks::doAction()
     */
    public static function doAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Hooks->doAction(...$args);
    }
}
