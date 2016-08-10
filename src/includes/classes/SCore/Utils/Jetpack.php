<?php
/**
 * Jetpack utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
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
 * Jetpack utils.
 *
 * @since 160720 Jetpack utils.
 */
class Jetpack extends Classes\SCore\Base\Core
{
    /**
     * Jetpack markdown class.
     *
     * @since 160720 Jetpack utils.
     *
     * @param \WPCom_Markdown|null|bool
     */
    protected $WPCom_Markdown;

    /**
     * Markdown via Jetpack.
     *
     * @since 160720 Jetpack utils.
     *
     * @param string $markdown Markdown.
     *
     * @return string HTML from markdown.
     */
    public function markdown(string $markdown): string
    {
        if (!$markdown) {
            return $markdown; // Not necessary.
        } elseif (!$this->canMarkdown() || !$this->WPCom_Markdown) {
            return $markdown; // Not possible.
        }
        return (string) $this->WPCom_Markdown->transform($markdown, ['unslash' => false]);
    }

    /**
     * Can markdown via Jetpack?
     *
     * @since 160720 Jetpack utils.
     *
     * @return bool Can markdown via Jetpack?
     */
    public function canMarkdown(): bool
    {
        if (!isset($this->WPCom_Markdown)) {
            $this->WPCom_Markdown = $this->Wp->is_jetpack_active && class_exists('WPCom_Markdown')
                ? \WPCom_Markdown::get_instance() : false;
        }
        return (bool) $this->WPCom_Markdown;
    }

    /**
     * Can LaTeX via Jetpack?
     *
     * @since 160720 Jetpack utils.
     *
     * @return bool Can LaTeX via Jetpack?
     */
    public function canLatex(): bool
    {
        return $this->Wp->is_jetpack_active && $this->c::canCallFunc('latex_markup');
    }
}
