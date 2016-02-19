<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function is_menu_page(...$args)
{
    return $GLOBALS[App::class]->Utils->MenuPage->is(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function current_menu_page(...$args)
{
    return $GLOBALS[App::class]->Utils->MenuPage->current(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function menu_page_now(...$args)
{
    return $GLOBALS[App::class]->Utils->MenuPage->now(...$args);
}
