<?php
/**
 * Post meta.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
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

/**
 * Post meta.
 *
 * @since 160723
 */
trait PostMeta
{
    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::key()
     */
    public static function postMetaKey(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->key(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::get()
     */
    public static function getPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->get(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::update()
     */
    public static function updatePostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->update(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::delete()
     */
    public static function deletePostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->delete(...$args);
    }

    /**
     * @since 160731 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::collect()
     */
    public static function collectPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->collect(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::set()
     */
    public static function setPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->set(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMeta::unset()
     */
    public static function unsetPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->unset(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMetaBox::add()
     */
    public static function addPostMetaBox(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMetaBox->add(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\PostMetaBox::form()
     */
    public static function postMetaBoxForm(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMetaBox->form(...$args);
    }
}
