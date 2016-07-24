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

trait PostMeta
{
    /**
     * @since 160723 Post meta utils.
     */
    public static function postMetaKey(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->key(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function getPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->get(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function updatePostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->update(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function deletePostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->delete(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function setPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->set(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function unsetPostMeta(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMeta->unset(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function addPostMetaBox(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMetaBox->add(...$args);
    }

    /**
     * @since 160723 Post meta utils.
     */
    public static function postMetaBoxForm(...$args)
    {
        return $GLOBALS[static::class]->Utils->§PostMetaBox->form(...$args);
    }
}
