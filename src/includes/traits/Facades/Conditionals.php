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

trait Conditionals
{
    /**
     * @since 160525 Initial release.
     */
    public static function isFront()
    {
        return !is_admin();
    }

    /**
     * @since 160525 Initial release.
     */
    public static function doingAjax()
    {
        return defined('DOING_AJAX') && DOING_AJAX;
    }

    /**
     * @since 160525 Initial release.
     */
    public static function isFrontOrAjax()
    {
        return !is_admin() || (defined('DOING_AJAX') && DOING_AJAX);
    }
}
