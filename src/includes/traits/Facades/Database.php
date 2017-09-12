<?php
/**
 * Database.
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
 * Database.
 *
 * @since 160524
 */
trait Database
{
    /**
     * @since 160524 Initial release.
     * @see Classes\SCore\Utils\Database::$wp
     */
    public static function wpDb()
    {
        return $GLOBALS[static::class]->Utils->§Database->wp;
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::prefix()
     */
    public static function dbPrefix(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->prefix(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::createTables()
     */
    public static function createDbTables(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->createTables(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::dropTables()
     */
    public static function dropDbTables(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->dropTables(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::ifNotExists()
     */
    public static function dbIfNotExists(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->ifNotExists(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::engineCompat()
     */
    public static function dbEngineCompat(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->engineCompat(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Database::charsetCompat()
     */
    public static function dbCharsetCompat(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Database->charsetCompat(...$args);
    }
}
