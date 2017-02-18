<?php
/**
 * Options.
 *
 * @author @jaswsinc
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
 * Options.
 *
 * @since 160524
 */
trait Options
{
    /**
     * @since 160524 App.
     * @see Classes/App::$Config
     */
    public static function options()
    {
        return $GLOBALS[static::class]->Config->§options;
    }

    /**
     * @since 160524 App.
     * @see Classes/App::$Config
     */
    public static function defaultOptions()
    {
        return $GLOBALS[static::class]->Config->§default_options;
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::get()
     */
    public static function getOption(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->get(...$args);
    }

    /**
     * @since 160826 Default option getter.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::getDefault()
     */
    public static function getDefaultOption(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->getDefault(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::merge()
     */
    public static function mergeOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->merge(...$args);
    }

    /**
     * @since 160524 App.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::update()
     */
    public static function updateOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->update(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::restoreDefaults()
     */
    public static function restoreDefaultOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->restoreDefaults(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::saveUrl()
     */
    public static function saveOptionsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->saveUrl(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::saveViaAjaxUrl()
     */
    public static function saveOptionsViaAjaxUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->saveViaAjaxUrl(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Options::restoreDefaultsUrl()
     */
    public static function restoreDefaultOptionsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->restoreDefaultsUrl(...$args);
    }
}
