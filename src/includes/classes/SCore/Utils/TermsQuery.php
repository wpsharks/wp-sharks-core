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
 * Term query utils.
 *
 * @since 160524 Term utils.
 */
class TermsQuery extends Classes\SCore\Base\Core
{
    /**
     * Total terms.
     *
     * @since 160524 Term utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total terms.
     */
    public function total(array $args = []): int
    {
        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'taxonomy_filters'   => [],
            'taxonomies_include' => [],
            'taxonomies_exclude' => [],
            'filters'            => [
                'get'        => 'all',
                'hide_empty' => false,
                'orderby'    => 'name',
            ],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['taxonomy_filters']   = (array) $args['taxonomy_filters'];
        $args['taxonomies_include'] = (array) $args['taxonomies_include'];
        $args['taxonomies_exclude'] = (array) $args['taxonomies_exclude'];
        $args['filters']            = (array) $args['filters'];
        $args['no_cache']           = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish total terms in the query.

        $taxonomies = get_taxonomies($args['taxonomy_filters'], 'objects');
        $taxonomies = $args['taxonomies_include'] ? array_intersect_key($taxonomies, array_fill_keys($args['taxonomies_include'], true)) : $taxonomies;
        $taxonomies = $args['taxonomies_exclude'] ? array_diff_key($taxonomies, array_fill_keys($args['taxonomies_exclude'], true)) : $taxonomies;

        $terms = get_terms(array_merge(['taxonomy' => array_keys($taxonomies)], $args['filters'], ['fields' => 'all']));
        $terms = is_array($terms) ? $terms : []; // Force array.

        return $total = count($terms);
    }

    /**
     * All terms.
     *
     * @since 160524 Term utils.
     *
     * @param array $args Behavioral args.
     *
     * @return \WP_Term[] Array of terms.
     */
    public function all(array $args = []): array
    {
        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
            'taxonomy_filters'   => [],
            'taxonomies_include' => [],
            'taxonomies_exclude' => [],
            'filters'            => [
                'get'        => 'all',
                'hide_empty' => false,
                'orderby'    => 'name',
            ],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Also used by {@link total()}.
        $args['taxonomy_filters']   = (array) $args['taxonomy_filters'];
        $args['taxonomies_include'] = (array) $args['taxonomies_include'];
        $args['taxonomies_exclude'] = (array) $args['taxonomies_exclude'];
        $args['filters']            = (array) $args['filters'];
        $args['no_cache']           = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($terms = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $terms; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $terms = []; // Fail; too many.
        }
        // Return the array of all term objects now.

        $taxonomies = get_taxonomies($args['taxonomy_filters'], 'objects');
        $taxonomies = $args['taxonomies_include'] ? array_intersect_key($taxonomies, array_fill_keys($args['taxonomies_include'], true)) : $taxonomies;
        $taxonomies = $args['taxonomies_exclude'] ? array_diff_key($taxonomies, array_fill_keys($args['taxonomies_exclude'], true)) : $taxonomies;

        $terms = get_terms(array_merge(['taxonomy' => array_keys($taxonomies)], $args['filters'], ['fields' => 'all']));
        $terms = is_array($terms) ? $terms : []; // Force array.

        return $terms;
    }

    /**
     * Term select options.
     *
     * @since 160524 Term utils.
     *
     * @param array $args Behavioral args.
     *
     * @return string Select options.
     */
    public function selectOptions(array $args = []): string
    {
        // In an admin area?

        $is_admin = is_admin();

        // Establish args.

        $default_args = [
            // Unique.
            'allow_empty'              => true,
            'allow_arbitrary'          => true,
            'option_child_indent_char' => '-',
            'option_formatter'         => null,
            'current_tax_term_ids'     => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'taxonomy_filters'   => !$is_admin ? ['public' => true] : [],
            'taxonomies_include' => [],
            'taxonomies_exclude' => [],
            'filters'            => [
                'get'        => 'all',
                'hide_empty' => false,
                'orderby'    => 'name',
            ],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']              = (bool) $args['allow_empty'];
        $args['allow_arbitrary']          = (bool) $args['allow_arbitrary'];
        $args['option_child_indent_char'] = (string) $args['option_child_indent_char'];
        $args['option_formatter']         = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_tax_term_ids']     = isset($args['current_tax_term_ids']) ? (array) $args['current_tax_term_ids'] : null;
        $args['current_tax_term_ids']     = $this->c::removeEmptys(array_map('strval', $args['current_tax_term_ids']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['taxonomy_filters']   = (array) $args['taxonomy_filters'];
        $args['taxonomies_include'] = (array) $args['taxonomies_include'];
        $args['taxonomies_exclude'] = (array) $args['taxonomies_exclude'];
        $args['filters']            = (array) $args['filters'];
        $args['no_cache']           = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($terms = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize working variables needed below.

        $options                = ''; // Initialize.
        $available_tax_term_ids = []; // Initialize.
        $selected_tax_term_ids  = []; // Initialize.

        $default_post_type_label = __('Post', 'wp-sharks-core');
        $default_tax_label       = __('Taxonomy', 'wp-sharks-core');
        $default_term_label      = __('Term', 'wp-sharks-core');

        $post_types = get_post_types([], 'objects'); // Everything.
        $taxonomies = get_taxonomies($args['taxonomy_filters'], 'objects');

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow ``?
            $options = '<option value=""></option>';
        }
        $walk = function (// Recursive parent/child walker.
            int $parent_term_id = 0,
            int $parent_depth = 0
        ) use (
            &$walk,
            &$is_admin,
            &$args,
            &$terms,
            &$options,
            &$available_tax_term_ids,
            &$selected_tax_term_ids,
            &$default_post_type_label,
            &$default_tax_label,
            &$default_term_label,
            &$post_types,
            &$taxonomies
        ) {
            foreach ($terms as $_term_object) { // \WP_Term objects.
                if ((int) $_term_object->parent !== $parent_term_id) {
                    continue; // Bypass this child for now.
                }
                $_tax_term_id             = $_term_object->taxonomy.':'.$_term_object->term_id;
                $available_tax_term_ids[] = $_tax_term_id; // Record all available.

                if (isset($args['current_tax_term_ids']) && in_array($_tax_term_id, $args['current_tax_term_ids'], true)) {
                    $selected_tax_term_ids[$_tax_term_id] = $_tax_term_id; // Flag selected post type.
                }
                $_post_type_label = !empty($taxonomies[$_term_object->taxonomy]->object_type[0])
                    && !empty($post_types[$taxonomies[$_term_object->taxonomy]->object_type[0]]->labels->singular_name)
                        ? $post_types[$taxonomies[$_term_object->taxonomy]->object_type[0]]->labels->singular_name : $default_post_type_label;

                $_tax_label = !empty($_term_object->taxonomy)
                    && !empty($taxonomies[$_term_object->taxonomy]->labels->singular_name)
                        ? $taxonomies[$_term_object->taxonomy]->labels->singular_name : $default_tax_label;

                $_term_label                = !empty($_term_object->name) ? $_term_object->name : $default_term_label;
                $_tax_term_id_selected_attr = isset($selected_tax_term_ids[$_tax_term_id]) ? ' selected' : '';

                // Format `<option>` tag w/ a custom formatter?

                if ($args['option_formatter']) {
                    $options .= $args['option_formatter']($_tax_term_id, $_term_object, [
                            'parent_term_id'            => $parent_term_id,
                            'parent_depth'              => $parent_depth,
                            'post_type_label'           => $_post_type_label,
                            'tax_label'                 => $_tax_label,
                            'term_label'                => $_term_label,
                            'tax_term_id_selected_attr' => $_tax_term_id_selected_attr,
                        ], $args); // ↑ This allows for a custom option formatter.
                        // The formatter must always return an `<option></option>` tag.

                // Else format the `<option>` tag using a default behavior.
                } elseif ($is_admin) { // Slightly different format in admin area.
                    $options .= '<option value="'.esc_attr($_tax_term_id).'"'.$_tax_term_id_selected_attr.'>'.
                                    ($parent_depth > 0 ? str_repeat('&nbsp;', $parent_depth).$args['option_child_indent_char'].' ' : '').
                                    esc_html($_post_type_label.' '.$_tax_label.' #'.$_term_object->term_id.': '.$_term_label).
                                '</option>';
                } else { // Front-end display should be friendlier in some ways.
                    $options .= '<option value="'.esc_attr($_tax_term_id).'"'.$_tax_term_id_selected_attr.'>'.
                                    ($parent_depth > 0 ? str_repeat('&nbsp;', $parent_depth).$args['option_child_indent_char'].' ' : '').
                                    esc_html($_post_type_label.' '.$_tax_label.': '.$_term_label).
                                '</option>';
                }
                $walk((int) $_term_object->term_id, $parent_depth + 1); // Any children this term has.
                //
            } // unset($_tax_term_id, $_term_object, $_post_type_label, $_tax_label, $_term_label, $_tax_term_id_selected_attr); // Housekeeping.
        };
        $walk(0, 0); // Start walking/building the parent » child `<option>` tags.

        if ($args['allow_arbitrary'] && $args['current_tax_term_ids']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_tax_term_ids'], $available_tax_term_ids) as $_arbitrary_tax_term_id) {
                $options .= '<option value="'.esc_attr($_arbitrary_tax_term_id).'" selected>'.
                                esc_html($default_post_type_label.' '.$default_tax_label.' # '.preg_replace('/^.*?\:/u', '', $_arbitrary_tax_term_id)).
                            '</option>';
            } // unset($_arbitrary_tax_term_id); // Housekeeping.
        }
        return $options; // HTML markup.
    }
}
