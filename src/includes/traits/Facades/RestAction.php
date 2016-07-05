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

trait RestAction
{
    /**
     * @since 160705 ReST utils.
     */
    public static function restActionVar(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->var;
    }

    /**
     * @since 160705 ReST utils.
     */
    public static function restActionDataVar(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->data_var;
    }

    /**
     * @since 160625 ReST utils.
     */
    public static function restActionApiVersion(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->apiVersion(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function restActionData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->data(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function bestRestActionUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->bestUrl(...$args);
    }

    /**
     * @since 160705 ReST utils.
     */
    public static function restActionUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->urlAdd(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function addUrlRestAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->urlAdd(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function removeUrlRestAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->urlRemove(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function restActionFormElementId(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->formElementId(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function restActionFormElementClass(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->formElementClass(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function restActionFormElementName(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->formElementName(...$args);
    }

    /**
     * @since 160608 ReST utils.
     */
    public static function registerRestAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§RestAction->register(...$args);
    }
}
