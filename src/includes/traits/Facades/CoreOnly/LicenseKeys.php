<?php
/**
 * License keys.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Traits\Facades\CoreOnly;

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
 * License keys.
 *
 * @since 160710
 */
trait LicenseKeys
{
    /**
     * @since 160710 License key utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\LicenseKeys::activate()
     */
    public static function activateLicenseKey(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\LicenseKeys'}->activate(...$args);
    }

    /**
     * @since 160710 License key utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\LicenseKeys::deactivate()
     */
    public static function deactivateLicenseKey(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\LicenseKeys'}->deactivate(...$args);
    }

    /**
     * @since 160710 License key utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\LicenseKeys::maybeRequestViaNotice()
     */
    public static function maybeRequestLicenseKeyViaNotice(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\LicenseKeys'}->maybeRequestViaNotice(...$args);
    }

    /**
     * @since 160712 License key utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\LicenseKeys::requestViaNoticeIsApplicable()
     */
    public static function licenseKeyRequestViaNoticeIsApplicable(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\LicenseKeys'}->requestViaNoticeIsApplicable(...$args);
    }

    /**
     * @since 160712 License key utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\CoreOnly\LicenseKeys::requestViaNoticeMarkup()
     */
    public static function licenseKeyRequestViaNoticeMarkup(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\LicenseKeys'}->requestViaNoticeMarkup(...$args);
    }
}
