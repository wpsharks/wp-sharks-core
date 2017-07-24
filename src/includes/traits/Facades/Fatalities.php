<?php
/**
 * Fatalities.
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
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Fatalities.
 *
 * @since 160606
 */
trait Fatalities
{
    /**
     * @since 17xxxx Fatalities.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Fatalities::die()
     */
    public static function die(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Fatalities->die(...$args);
    }

    /**
     * @since 160606 Fatalities.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Fatalities::dieInvalid()
     */
    public static function dieInvalid(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Fatalities->dieInvalid(...$args);
    }

    /**
     * @since 160524 Fatalities.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Fatalities::dieForbidden()
     */
    public static function dieForbidden(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Fatalities->dieForbidden(...$args);
    }
}
