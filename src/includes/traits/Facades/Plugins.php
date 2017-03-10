<?php
/**
 * Plugins.
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
 * Plugins.
 *
 * @since 160524
 */
trait Plugins
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugins::active()
     */
    public static function activePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->active(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugins::networkActive()
     */
    public static function networkActivePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->networkActive(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugins::allActive()
     */
    public static function allActivePlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->allActive(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Plugins::allInstalled()
     */
    public static function allInstalledPlugins(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Plugins->allInstalled(...$args);
    }
}
