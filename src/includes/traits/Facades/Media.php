<?php
/**
 * Media utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
//
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
//
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
//
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Media utils.
 *
 * @since 17xxxx
 */
trait Media
{
    /**
     * @since 17xxxx Media utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Media::addAttachmentFromUrl()
     */
    public static function addAttachmentFromUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Media->addAttachmentFromUrl(...$args);
    }

    /**
     * @since 17xxxx Media utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Media::imageSpecs()
     */
    public static function imageSpecs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Media->imageSpecs(...$args);
    }

    /**
     * @since 17xxxx Media utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Media::postThumbnailSpecs()
     */
    public static function postThumbnailSpecs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Media->postThumbnailSpecs(...$args);
    }

    /**
     * @since 17xxxx Media utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\Media::thumbnailSpecs()
     */
    public static function thumbnailSpecs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§Media->thumbnailSpecs(...$args);
    }
}
