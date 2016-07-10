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
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Menu page utils.
 *
 * @since 160524 Menu page utils.
 */
class MenuPageMarkup extends Classes\SCore\Base\Core
{
    /**
     * Hover tip; via jQuery UI tooltip.
     *
     * @since 160524 Menu page markup utils.
     *
     * @param string $tip Tip (can contain HTML markup).
     *
     * @return string Raw HTML markup.
     */
    public function tip(string $tip): string
    {
        return '<i class="-tip" data-toggle="core.jquery-ui-tooltip" title="'.esc_attr($tip).'"></i>';
    }

    /**
     * Note (smaller text).
     *
     * @since 160524 Menu page markup utils.
     *
     * @param string $note Note (can contain HTML markup).
     *
     * @return string Raw HTML markup.
     */
    public function note(string $note): string
    {
        return '<div class="-note">'.$note.'</div>';
    }

    /**
     * A menu page form class instance.
     *
     * @since 160524 Menu page markup utils.
     *
     * @param string $action ReST action identifier.
     *
     * @return Classes\SCore\MenuPageForm Class instance.
     */
    public function form(string $action): Classes\SCore\MenuPageForm
    {
        return $this->c::diGet(Classes\SCore\MenuPageForm::class, compact('action'));
    }
}
