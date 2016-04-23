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
 * Scripts/styles.
 *
 * @since 16xxxx Scripts/styles.
 */
class StylesScripts extends Classes\SCore\Base\Core
{
    /**
     * Enqueue jQuery Chosen scripts.
     *
     * @since 16xxxx jQuery Chosen plugin.
     */
    public function enqueueJQueryChosen()
    {
        wp_enqueue_style('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css', [], null, 'all');
        wp_enqueue_script('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.min.js', ['jquery'], null, true);

        wp_localize_script(
            'jquery-chosen',
            'jquery_chosen_i18n',
            [
                'no_results_text'           => __('No results match', 'wp-sharks-core'),
                'placeholder_text_multiple' => __('Select Some Options', 'wp-sharks-core'),
                'placeholder_text_single'   => __('Select an Option', 'wp-sharks-core'),
            ]
        );
    }
}
