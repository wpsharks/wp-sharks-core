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

trait Action
{
    /**
     * @since 160531 Action utils.
     */
    public static function actionData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->data(...$args);
    }

    /**
     * @since 160531 Action utils.
     */
    public static function addUrlAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->urlAdd(...$args);
    }

    /**
     * @since 160531 Action utils.
     */
    public static function removeUrlAction(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->urlRemove(...$args);
    }

    /**
     * @since 160531 Action utils.
     */
    public static function actionFormElementId(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->formElementId(...$args);
    }

    /**
     * @since 160531 Action utils.
     */
    public static function actionFormElementClass(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->formElementClass(...$args);
    }

    /**
     * @since 160531 Action utils.
     */
    public static function actionFormElementName(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Action->formElementName(...$args);
    }
}
