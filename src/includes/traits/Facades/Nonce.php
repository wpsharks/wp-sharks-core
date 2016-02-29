<?php
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

trait Nonce
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function addUrlNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->urlAdd(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function removeUrlNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->urlRemove(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function isNonceValid(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->isValid(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function requireValidNonce(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Nonce->requireValid(...$args);
    }
}
