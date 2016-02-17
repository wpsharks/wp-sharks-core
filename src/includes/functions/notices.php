<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Adding notice utils.
 */
function enqueue_notice()
{
    return $GLOBALS[App::class]->Utils->WpNotices->enqueue(...$args);
}
