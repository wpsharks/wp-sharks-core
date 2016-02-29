<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Hook utils.
 *
 * @since 16xxxx Hook utils.
 */
class Hooks extends Classes\SCore\Base\Core
{
    /**
     * Apply filters.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $hook  A hook.
     * @param mixed  $value Value to filter.
     * @param mixed ...$args Any additional args.
     *
     * @return mixed Filtered `$value`.
     */
    public function applyFilters(string $hook, $value, ...$args)
    {
        return apply_filters($this->App->Config->©brand['©var'].'_'.$hook, $value, ...$args);
    }

    /**
     * Do an action.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $hook A hook.
     * @param mixed ...$args Any additional args.
     */
    public function doAction(string $hook, ...$args)
    {
        do_action($this->App->Config->©brand['©var'].'_'.$hook, ...$args);
    }
}
