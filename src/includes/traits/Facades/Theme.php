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

trait Theme
{
    /**
     * @since 16xxxx Initial release.
     */
    public static function themeIsInstalled(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Theme->isInstalled(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function installedThemeData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Theme->installedData(...$args);
    }
}
