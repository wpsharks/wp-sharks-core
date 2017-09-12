<?php
/**
 * Widget utils.
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
 * Widget utils.
 *
 * @since 160729 Widget utils.
 */
class Widget extends Classes\SCore\Base\Core
{
    /**
     * A widget form class instance.
     *
     * @since 160729 Widget utils.
     *
     * @param Classes\SCore\Base\Widget $Widget A widget instance.
     * @param array                     $args   Any additional behavioral args.
     *
     * @return Classes\SCore\WidgetForm Class instance.
     */
    public function form(Classes\SCore\Base\Widget $Widget, array $args = []): Classes\SCore\WidgetForm
    {
        return $this->App->Di->get(Classes\SCore\WidgetForm::class, compact('Widget', 'args'));
    }
}
