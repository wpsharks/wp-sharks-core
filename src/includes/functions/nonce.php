<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Functions;

use WebSharks\WpSharks\Core\Classes\App;

/**
 * @since 16xxxx Initial release.
 */
function add_url_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->Nonce->urlAdd(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function remove_url_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->Nonce->urlRemove(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function is_nonce_valid(...$args)
{
    return $GLOBALS[App::class]->Utils->Nonce->isValid(...$args);
}

/**
 * @since 16xxxx Initial release.
 */
function require_valid_nonce(...$args)
{
    return $GLOBALS[App::class]->Utils->Nonce->requireValid(...$args);
}
