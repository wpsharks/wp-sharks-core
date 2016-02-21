<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function date_i18n(...$args)
{
    return $GLOBALS[App::class]->Utils->WpDate->i18n(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function date_i18n_utc(...$args)
{
    return $GLOBALS[App::class]->Utils->WpDate->i18nUtc(...$args);
}
