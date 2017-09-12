<?php
/**
 * Hook utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

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
 * Hook utils.
 *
 * @since 160524 Hook utils.
 */
class Hooks extends Classes\SCore\Base\Core
{
    /**
     * Add a filter.
     *
     * @since 160524 Initial release.
     *
     * @param string   $hook     A hook.
     * @param callable $callable Callable.
     * @param mixed    ...$args  Any additional args.
     *
     * @return bool See {@link add_filter()}
     */
    public function addFilter(string $hook, callable $callable, ...$args)
    {
        return add_filter($this->App->Config->©brand['©var'].'_'.$hook, $callable, ...$args);
    }

    /**
     * Apply filters.
     *
     * @since 160524 Initial release.
     *
     * @param string $hook    A hook.
     * @param mixed  $value   Value to filter.
     * @param mixed  ...$args Any additional args.
     *
     * @return mixed Filtered `$value`.
     */
    public function applyFilters(string $hook, $value, ...$args)
    {
        return apply_filters($this->App->Config->©brand['©var'].'_'.$hook, $value, ...$args);
    }

    /**
     * Add an action.
     *
     * @since 160524 Initial release.
     *
     * @param string   $hook     A hook.
     * @param callable $callable Callable.
     * @param mixed    ...$args  Any additional args.
     *
     * @return bool See {@link add_action()}
     */
    public function addAction(string $hook, callable $callable, ...$args)
    {
        return add_action($this->App->Config->©brand['©var'].'_'.$hook, $callable, ...$args);
    }

    /**
     * Do an action.
     *
     * @since 160524 Initial release.
     *
     * @param string $hook    A hook.
     * @param mixed  ...$args Any additional args.
     */
    public function doAction(string $hook, ...$args)
    {
        do_action($this->App->Config->©brand['©var'].'_'.$hook, ...$args);
    }
}
