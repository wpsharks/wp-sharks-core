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
#
use function assert as debug;
use function get_defined_vars as vars;

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
    public static function dbIfNotExists(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->ifNotExists(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function dbEngineCompat(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->engineCompat(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function dbCharsetCompat(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->charsetCompat(...$args);
    }
}
