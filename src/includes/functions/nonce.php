<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function add_url_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->WpNonce->urlAdd(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function remove_url_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->WpNonce->urlRemove(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function is_nonce_valid(...$args)
{
    return $GLOBALS[App::class]->Utils->WpNonce->isValid(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function require_valid_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->WpNonce->requireValid(...$args);
}
