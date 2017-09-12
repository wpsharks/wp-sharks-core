<?php
/**
 * Menu page markup.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Menu page markup.
 *
 * @since 160709
 */
trait MenuPageMarkup
{
    /**
     * @since 160709 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPageMarkup::note()
     */
    public static function menuPageNote(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPageMarkup->note(...$args);
    }

    /**
     * @since 160709 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPageMarkup::tip()
     */
    public static function menuPageTip(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPageMarkup->tip(...$args);
    }

    /**
     * @since 160711 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPageMarkup::noticeErrors()
     */
    public static function menuPageNoticeErrors(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPageMarkup->noticeErrors(...$args);
    }
}
