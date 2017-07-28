<?php
/**
 * WP user utils.
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
 * WP user utils.
 *
 * @since 17xxxx WP user utils.
 */
trait WpUser
{
    /**
     * @since 17xxxx WP user utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WpUser::loginAs()
     */
    public static function loginAsUser(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WpUser->loginAs(...$args);
    }

    /**
     * @since 17xxxx WP user utils.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\SCore\Utils\WpUser::isCurrent()
     */
    public static function isCurrentUser(...$args)
    {
        return $GLOBALS[static::class]->Utils->§WpUser->isCurrent(...$args);
    }
}
