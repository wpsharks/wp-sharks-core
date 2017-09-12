<?php
/**
 * Date.
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
 * Date.
 *
 * @since 160524
 */
trait Date
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Date::i18n()
     */
    public static function dateI18n(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Date->i18n(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Date::i18nUtc()
     */
    public static function dateI18nUtc(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Date->i18nUtc(...$args);
    }

    /**
     * @since 160702 UTC conversions.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Date::toUtc()
     */
    public static function localToUtc(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Date->toUtc(...$args);
    }

    /**
     * @since 160702 UTC conversions.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Date::toLocal()
     */
    public static function utcToLocal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Date->toLocal(...$args);
    }
}
