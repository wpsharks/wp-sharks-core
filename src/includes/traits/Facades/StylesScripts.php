<?php
/**
 * Styles/scripts.
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
     * @since 170218.31677 Styles/scripts.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueLibs()
     */
    public static function enqueueLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueLibs(...$args);
    }

    /**
     * @since 170218.31677 Styles/scripts.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::didEnqueueLibs()
     */
    public static function didEnqueueLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->didEnqueueLibs(...$args);
    }

    /**
     * @since 170218.31677 Styles/scripts.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::didEnqueueStyle()
     */
    public static function didEnqueueStyle(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->didEnqueueStyle(...$args);
    }

    /**
     * @since 170218.31677 Styles/scripts.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::didEnqueueScript()
     */
    public static function didEnqueueScript(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->didEnqueueScript(...$args);
    }

    /**
     * @since 170218.31677 Require.js.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueRequireJsLibs()
     */
    public static function enqueueRequireJsLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueRequireJsLibs(...$args);
    }

    /**
     * @since 170128.18158 Latest jQuery.
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
     * @since 170329.20871 Latest Underscore.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueLatestUnderscore()
     */
    public static function enqueueLatestUnderscore(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueLatestUnderscore(...$args);
    }

    /**
     * @since 170218.31677 Unicode Gcs libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueUnicodeGcsLibs()
     */
    public static function enqueueUnicodeGcsLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueUnicodeGcsLibs(...$args);
    }

    /**
     * @since 170218.31677 Pako inflate/deflate.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueuePakoLibs()
     */
    public static function enqueuePakoLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueuePakoLibs(...$args);
    }

    /**
     * @since 170128.18158 Font Awesome libs.
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
     * @since 170417.43553 Devicon libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueDeviconLibs()
     */
    public static function enqueueDeviconLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueDeviconLibs(...$args);
    }

    /**
     * @since 170128.18158 Semantic UI libs.
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
     * @since 170128.18158 Behave libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueBehaveLibs()
     */
    public static function enqueueBehaveLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueBehaveLibs(...$args);
    }

    /**
     * @since 170218.31677 Ace libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueAceLibs()
     */
    public static function enqueueAceLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueAceLibs(...$args);
    }

    /**
     * @since 170128.18158 Markdown It libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueMarkdownItLibs()
     */
    public static function enqueueMarkdownItLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueMarkdownItLibs(...$args);
    }

    /**
     * @since 170218.31677 Highlight.js style data.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::highlightJsStyleData()
     */
    public static function highlightJsStyleData(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->highlightJsStyleData(...$args);
    }

    /**
     * @since 170128.18158 Highlight.js libs.
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
     * @since 170329.20871 Stripe libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueStripeLibs()
     */
    public static function enqueueStripeLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueStripeLibs(...$args);
    }

    /**
     * @since 170311.43193 reCAPTCHA libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueRecaptchaLibs()
     */
    public static function enqueueRecaptchaLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueRecaptchaLibs(...$args);
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
     * @since 170329.20871 No UI Slider libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueNoUiSliderLibs()
     */
    public static function enqueueNoUiSliderLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueNoUiSliderLibs(...$args);
    }

    /**
     * @since 170329.20871 Adding jQuery Address libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueJQueryAddressLibs()
     */
    public static function enqueueJQueryAddressLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryAddressLibs(...$args);
    }

    /**
     * @since 170218.31677 Adding jQuery animate.css libs.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\StylesScripts::enqueueJQueryAnimateCssLibs()
     */
    public static function enqueueJQueryAnimateCssLibs(...$args)
    {
        return $GLOBALS[static::class]->Utils->§StylesScripts->enqueueJQueryAnimateCssLibs(...$args);
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
