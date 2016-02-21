<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function all_active_plugins(...$args)
{
    return $GLOBALS[App::class]->Utils->WpPlugins->active(...$args);
}
