<?php
/**
 * WP MD Extra utils.
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
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * WP MD Extra utils.
 *
 * @since 170126.83164 WP MDE utils.
 */
class WpMdExtra extends Classes\SCore\Base\Core
{
    /**
     * Transform via WP MD Extra.
     *
     * @since 170126.83164 WP MD Extra utils.
     *
     * @param string $markdown Markdown.
     * @param int    $post_id  Post ID (optional).
     * @param array  $args     Behavioral args (optional).
     *
     * @return string HTML from markdown.
     */
    public function transform(string $markdown, int $post_id = 0, array $args = []): string
    {
        global $wp_markdown_extra;
        $wpMde = $wp_markdown_extra;

        if (!$markdown) {
            return $markdown;
        } elseif (!$wpMde) {
            return $markdown;
        } elseif (!$this->canTransform()) {
            return $markdown;
        }
        return $wpMde->a::transform($markdown, $post_id, $args);
    }

    /**
     * WP MD Extra enabled?
     *
     * @since 170126.83164 WP MD Extra utils.
     *
     * @param string $for Enabled for what?
     *
     * @return bool Jetpack markdown enabled?
     */
    public function enabled(string $for = ''): bool
    {
        global $wp_markdown_extra;
        $wpMde = $wp_markdown_extra;

        $for = $for === 'posts' || $for === 'comments' ? $for : 'posts';
        return $wpMde && $wpMde->s::getOption($for.'_enable');
    }

    /**
     * Can transform via WP MD Extra?
     *
     * @since 170126.83164 WP MD Extra utils.
     *
     * @return bool Can transform via WP MDE?
     */
    public function canTransform(): bool
    {
        global $wp_markdown_extra;
        $wpMde = $wp_markdown_extra;

        return !empty($wpMde);
    }
}
