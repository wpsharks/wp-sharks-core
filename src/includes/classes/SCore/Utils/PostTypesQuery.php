<?php
/**
 * Post type query utils.
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
 * Post type query utils.
 *
 * @since 160524 Post utils.
 */
class PostTypesQuery extends Classes\SCore\Base\Core
{
    /**
     * Total post types.
     *
     * @since 160524 Post type utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total post types.
     */
    public function total(array $args = []): int
    {
        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'filters'  => [],
            'include'  => [],
            'exclude'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['filters']  = (array) $args['filters'];
        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish total post types in the query.

        $post_types = get_post_types($args['filters'], 'objects');
        $post_types = $args['include'] ? array_intersect_key($post_types, array_fill_keys($args['include'], true)) : $post_types;
        $post_types = $args['exclude'] ? array_diff_key($post_types, array_fill_keys($args['exclude'], true)) : $post_types;

        return $total = count($post_types);
    }

    /**
     * All post types.
     *
     * @since 160524 Post type utils.
     *
     * @param array $args Behavioral args.
     *
     * @return \StdClass[] Array of post types.
     */
    public function all(array $args = []): array
    {
        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
            'filters'  => [],
            'include'  => [],
            'exclude'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Also used by {@link total()}.
        $args['filters']  = (array) $args['filters'];
        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($post_types = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $post_types; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $post_types = []; // Fail; too many.
        }
        // Return the array of all post type objects now.

        $post_types = get_post_types($args['filters'], 'objects');
        $post_types = $args['include'] ? array_intersect_key($post_types, array_fill_keys($args['include'], true)) : $post_types;
        $post_types = $args['exclude'] ? array_diff_key($post_types, array_fill_keys($args['exclude'], true)) : $post_types;

        return $post_types;
    }

    /**
     * Post type select options.
     *
     * @since 160524 Post type utils.
     *
     * @param array $args Behavioral args.
     *
     * @return string Select options.
     */
    public function selectOptions(array $args = []): string
    {
        // Establish args.

        $default_args = [
            // Unique.
            'allow_empty'        => true,
            'allow_arbitrary'    => true,
            'option_formatter'   => null,
            'current_post_types' => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'filters' => !$this->Wp->is_admin
                ? ['public' => true, 'exclude_from_search' => false]
                : ['exclude_from_search' => false],
            'include'  => [],
            'exclude'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']        = (bool) $args['allow_empty'];
        $args['allow_arbitrary']    = (bool) $args['allow_arbitrary'];
        $args['option_formatter']   = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_post_types'] = isset($args['current_post_types']) ? (array) $args['current_post_types'] : null;
        $args['current_post_types'] = $this->c::removeEmptys(array_map('strval', $args['current_post_types']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['filters']  = (array) $args['filters'];
        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($post_types = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize several working variables needed below.

        $options                 = ''; // Initialize.
        $available_post_types    = []; // Initialize.
        $selected_post_types     = []; // Initialize.
        $default_post_type_label = __('Posts', 'wp-sharks-core');

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow ``?
            $options = '<option value=""></option>';
        }
        foreach ($post_types as $_post_type => $_post_type_object) { // \StdClass objects.
            $available_post_types[] = $_post_type; // Record all available.

            if (isset($args['current_post_types']) && in_array($_post_type, $args['current_post_types'], true)) {
                $selected_post_types[$_post_type] = $_post_type; // Flag selected post type.
            }
            $_post_type_label         = !empty($_post_type_object->labels->name) ? $_post_type_object->labels->name : $default_post_type_label;
            $_post_type_selected_attr = isset($selected_post_types[$_post_type]) ? ' selected' : '';

            // Format `<option>` tag w/ a custom formatter?

            if ($args['option_formatter']) {
                $options .= $args['option_formatter']($_post_type, $_post_type_object, [
                        'post_type_label'         => $_post_type_label,
                        'post_type_selected_attr' => $_post_type_selected_attr,
                    ], $args); // ↑ This allows for a custom option formatter.
                    // The formatter must always return an `<option></option>` tag.

            // Else format the `<option>` tag using a default behavior.
            } else { // Both front & back-end displays are the same for post types.
                $options .= '<option value="'.esc_attr($_post_type).'"'.$_post_type_selected_attr.'>'.
                                esc_html($_post_type_label).
                            '</option>';
            }
        } // unset($_post_type, $_post_type_object, $_post_type_label, $_post_selected_attr); // Housekeeping.

        if ($args['allow_arbitrary'] && $args['current_post_types']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_post_types'], $available_post_types) as $_arbitrary_post_type) {
                $options .= '<option value="'.esc_attr($_arbitrary_post_type).'" selected>'.
                                esc_html($default_post_type_label).
                            '</option>';
            } // unset($_arbitrary_post_type); // Housekeeping.
        }
        return $options; // HTML markup.
    }
}
