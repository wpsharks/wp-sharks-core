<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function is_menu_page(...$args)
{
    return $GLOBALS[App::class]->Utils->WpMenuPage->is(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function current_menu_page(...$args)
{
    return $GLOBALS[App::class]->Utils->WpMenuPage->current(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function menu_page_now(...$args)
{
    return $GLOBALS[App::class]->Utils->WpMenuPage->now(...$args);
}
