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

trait Database
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function wpDb()
    {
        return $GLOBALS[static::class]->Utils->§Database->wp;
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function dbPrefix(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->prefix(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function createDbTables(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->createTables(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function dropDbTables(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->dropTables(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function fulltextDbCompat(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->fulltextCompat(...$args);
    }
}
