<?php
/**
 * Styles/scripts.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
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
 * Styles/scripts.
 *
 * @since 160524
 */
trait StylesScripts
{
    /**
     * @since 17xxxx Latest jQuery.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueLatestJQuery()
     */
    public static function enqueueLatestJQuery(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueLatestJQuery(...$args);
    }

    /**
     * @since 17xxxx Font Awesome libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueFontAwesomeLibs()
     */
    public static function enqueueFontAwesomeLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueFontAwesomeLibs(...$args);
    }

    /**
     * @since 160709 Sharkicon libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueSharkiconLibs()
     */
    public static function enqueueSharkiconLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueSharkiconLibs(...$args);
    }

    /**
     * @since 17xxxx Semantic UI libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueSemanticUiLibs()
     */
    public static function enqueueSemanticUiLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueSemanticUiLibs(...$args);
    }

    /**
     * @since 17xxxx Highlight.js libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueHighlightJsLibs()
     */
    public static function enqueueHighlightJsLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueHighlightJsLibs(...$args);
    }

    /**
     * @since 17xxxx Simple MDE libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueSimpleMdeLibs()
     */
    public static function enqueueSimpleMdeLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueSimpleMdeLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueMomentLibs()
     */
    public static function enqueueMomentLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueMomentLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueJQueryPickadateLibs()
     */
    public static function enqueueJQueryPickadateLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryPickadateLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueJQueryChosenLibs()
     */
    public static function enqueueJQueryChosenLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryChosenLibs(...$args);
    }

    /**
     * @since 160524 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueJQueryJsGridLibs()
     */
    public static function enqueueJQueryJsGridLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryJsGridLibs(...$args);
    }

    /**
     * @since 160709 Menu page libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueMenuPageLibs()
     */
    public static function enqueueMenuPageLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueMenuPageLibs(...$args);
    }
}
