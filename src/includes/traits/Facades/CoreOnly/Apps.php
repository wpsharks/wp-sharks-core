<?php
/**
 * Apps.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Traits\Facades\CoreOnly;

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
 * Apps.
 *
 * @since 160710
 */
trait Apps
{
    /**
     * @since 160710 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\Apps::add()
     */
    public static function addApp(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->add(...$args);
    }

    /**
     * @since 160710 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\Apps::get()
     */
    public static function getApps(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->get(...$args);
    }

    /**
     * @since 160710 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\Apps::byType()
     */
    public static function getAppsByType(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->byType(...$args);
    }

    /**
     * @since 160710 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\Apps::bySlug()
     */
    public static function getAppsBySlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->bySlug(...$args);
    }

    /**
     * @since 160715 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\Apps::byNetworkWide()
     */
    public static function getAppsByNetworkWide(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->byNetworkWide(...$args);
    }
}
