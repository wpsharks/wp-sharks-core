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

trait WcOrderItem
{
    /**
     * @since 160608 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcOrderItem::orderByItemId()
     */
    public static function wcOrderByItemId(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcOrderItem->orderByItemId(...$args);
    }

    /**
     * @since 160608 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcOrderItem::orderItemById()
     */
    public static function wcOrderItemById(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcOrderItem->orderItemById(...$args);
    }

    /**
     * @since 160608 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcOrderItem::productIdFromItem()
     */
    public static function wcProductIdFromItem(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcOrderItem->productIdFromItem(...$args);
    }

    /**
     * @since 160608 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WcOrderItem::productByOrderItemId()
     */
    public static function wcProductByOrderItemId(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WcOrderItem->productByOrderItemId(...$args);
    }
}
