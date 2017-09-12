<?php
/**
 * WC product.
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
 * WC product.
 *
 * @since 160727
 */
trait WcProduct
{
    /**
     * @since 160727 WC product utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcProduct::idBySlug()
     */
    public static function wcProductIdBySlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcProduct->idBySlug(...$args);
    }

    /**
     * @since 160727 WC product utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcProduct::bySlug()
     */
    public static function wcProductBySlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcProduct->bySlug(...$args);
    }

    /**
     * @since 170420.14768 WC product utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcProduct::post()
     */
    public static function wcProductPost(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcProduct->post(...$args);
    }

    /**
     * @since 170420.14768 WC product utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcProduct::parent()
     */
    public static function wcProductParent(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcProduct->parent(...$args);
    }
}
