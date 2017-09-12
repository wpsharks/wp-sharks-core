<?php
/**
 * Jetpack utils.
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
     * @param array  $args     Behavioral args.
     *
     * @return string HTML from markdown.
     */
    public function markdown(string $markdown, array $args = []): string
    {
        if (!$markdown) {
            return $markdown;
        } elseif (!$this->canMarkdown()) {
            return $markdown;
        } elseif (!$this->WPCom_Markdown) {
            return $markdown;
        }
        $default_args = ['unslash' => false];
        $args += $default_args; // Merge defaults.

        return (string) $this->WPCom_Markdown->transform($markdown, $args);
    }

    /**
     * Jetpack markdown enabled?
     *
     * @since 170126.83164 Jetpack utils.
     *
     * @param string $for Enabled for what?
     *
     * @return bool Jetpack markdown enabled?
     */
    public function markdownEnabled(string $for = ''): bool
    {
        $for = $for === 'posts' || $for === 'comments' ? $for : 'posts';

        return $this->Wp->is_jetpack_active && \Jetpack::is_module_active('markdown')
            // Note: When the Markdown module is active, it's always on for `posts`.
            // See: `jetpack/modules/markdown.php` for the filter that enforces.
            // Note also: It's better not to run the `get_option()` call for `posts`.
            // The option value is only true after the module file/filter is loaded later.
            && ($for === 'posts' || \Jetpack::get_option('wpcom_publish_'.$for.'_with_markdown'));
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
        if (!isset($this->WPCom_Markdown)) { // Check and set instance at same time.
            $this->WPCom_Markdown = $this->Wp->is_jetpack_active && class_exists('WPCom_Markdown') ? \WPCom_Markdown::get_instance() : false;
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
