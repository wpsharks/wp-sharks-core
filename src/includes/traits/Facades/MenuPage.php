<?php
/**
 * Menu page.
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
 * Menu page.
 *
 * @since 160524
 */
trait MenuPage
{
    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::current()
     */
    public static function currentMenuPage(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->current(...$args);
    }

    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::now()
     */
    public static function menuPageNow(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->now(...$args);
    }

    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::is()
     */
    public static function isMenuPage(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->is(...$args);
    }

    /**
     * @since 160606 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::isOwn()
     */
    public static function isOwnMenuPage(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->isOwn(...$args);
    }

    /**
     * @since 160606 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::currentTab()
     */
    public static function currentMenuPageTab(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->currentTab(...$args);
    }

    /**
     * @since 160606 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::isTab()
     */
    public static function isMenuPageTab(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->isTab(...$args);
    }

    /**
     * @since 160606 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::isOwnTab()
     */
    public static function isOwnMenuPageTab(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->isOwnTab(...$args);
    }

    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::currentPostType()
     */
    public static function currentMenuPagePostType(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->currentPostType(...$args);
    }

    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::postTypeNow()
     */
    public static function menuPagePostTypeNow(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->postTypeNow(...$args);
    }

    /**
     * @since 160524 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::isForPostType()
     */
    public static function isMenuPageForPostType(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->isForPostType(...$args);
    }

    /**
     * @since 160709 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::addMenu()
     */
    public static function addMenuPage(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->addMenu(...$args);
    }

    /**
     * @since 160709 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::addMenuItem()
     */
    public static function addMenuPageItem(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->addMenuItem(...$args);
    }

    /**
     * @since 160712 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::url()
     */
    public static function menuPageUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->url(...$args);
    }

    /**
     * @since 161026 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::defaultUrl()
     */
    public static function defaultMenuPageUrl(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->defaultUrl(...$args);
    }

    /**
     * @since 160709 Menu page utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\MenuPage::form()
     */
    public static function menuPageForm(...$args)
    {
        return $GLOBALS[static::class]->Utils->§MenuPage->form(...$args);
    }
}
