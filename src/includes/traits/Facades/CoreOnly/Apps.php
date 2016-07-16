<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Traits\Facades\CoreOnly;

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

trait Apps
{
    /**
     * @since 160710 Initial release.
     */
    public static function addApp(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->add(...$args);
    }

    /**
     * @since 160710 Initial release.
     */
    public static function getApps(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->get(...$args);
    }

    /**
     * @since 160710 Initial release.
     */
    public static function getAppsByType(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->byType(...$args);
    }

    /**
     * @since 160710 Initial release.
     */
    public static function getAppsBySlug(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->bySlug(...$args);
    }

    /**
     * @since 160715 Initial release.
     */
    public static function getAppsByNetworkWide(...$args)
    {
        return $GLOBALS[static::class]->Utils->{'§CoreOnly\\Apps'}->byNetworkWide(...$args);
    }
}
