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

trait Options
{
    /**
     * @since 16xxxx App.
     */
    public static function options()
    {
        return $GLOBALS[static::class]->Config->§options;
    }

    /**
     * @since 16xxxx App.
     */
    public static function defaultOptions()
    {
        return $GLOBALS[static::class]->Config->§default_options;
    }

    /**
     * @since 16xxxx App.
     */
    public static function mergeOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->merge(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function updateOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->update(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function saveOptionsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->saveUrl(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function optionFormElementId(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->formElementId(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function optionFormElementName(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->formElementName(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function restoreDefaultOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->restoreDefaults(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function restoreDefaultOptionsUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Options->restoreDefaultsUrl(...$args);
    }
}
