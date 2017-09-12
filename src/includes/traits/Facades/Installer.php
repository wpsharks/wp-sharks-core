<?php
/**
 * Installer.
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
 * Installer.
 *
 * @since 160524
 */
trait Installer
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Installer::maybeInstall()
     */
    public static function maybeInstall(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Installer->maybeInstall(...$args);
    }

    /**
     * @since 161014 Trial routines.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Installer::maybeExpireTrial()
     */
    public static function maybeExpireTrial(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Installer->maybeExpireTrial(...$args);
    }

    /**
     * @since 161014 Trial routines.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Installer::isTrialExpired()
     */
    public static function isTrialExpired(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Installer->isTrialExpired(...$args);
    }

    /**
     * @since 161014 Trial routines.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Installer::trialDaysRemaining()
     */
    public static function trialDaysRemaining(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Installer->trialDaysRemaining(...$args);
    }

    /**
     * @since 161014 Trial routines.
     * @see Classes\SCore\Utils\Installer::$trial_days
     */
    public static function trialDays()
    {
        return $GLOBALS[static::class]->Utils->§Installer->trial_days;
    }
}
