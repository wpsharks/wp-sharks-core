<?php
/**
 * Nonce.
 *
 * @author @jaswsinc
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
 * Nonce.
 *
 * @since 160524
 */
trait Nonce
{
    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Nonce::urlAdd()
     */
    public static function addUrlNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->urlAdd(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Nonce::urlRemove()
     */
    public static function removeUrlNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->urlRemove(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Nonce::isValid()
     */
    public static function isNonceValid(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->isValid(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Nonce::requireValid()
     */
    public static function requireValidNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->requireValid(...$args);
    }
}
