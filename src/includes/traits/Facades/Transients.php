<?php
/**
 * Transients.
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
 * Transients.
 *
 * @since 160524
 */
trait Transients
{
    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Transient::get()
     */
    public static function getTransient(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Transient->get(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Transient::set()
     */
    public static function setTransient(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Transient->set(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Transient::delete()
     */
    public static function deleteTransient(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Transient->delete(...$args);
    }
}
