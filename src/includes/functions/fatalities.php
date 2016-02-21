<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function die_forbidden(...$args)
{
    return $GLOBALS[App::class]->Utils->Fatalities->forbidden(...$args);
}
