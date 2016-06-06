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
 * Post query utils.
 *
 * @since 160524 Post utils.
 */
class PostsQuery extends Classes\SCore\Base\Core
{
    /**
     * Total posts.
     *
     * @since 160524 Post query utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total posts.
     */
    public function total(array $args = []): int
    {
        $WpDb = $this->s::wpDb();

        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'for_comments_only' => false,

            'include_post_ids' => [],
            'exclude_post_ids' => [],

            'include_post_types' => [],
            'exclude_post_types' => [],

            'include_post_statuses' => [],
            'exclude_post_statuses' => [],

            'exclude_drafts'             => true,
            'exclude_revisions'          => true,
            'exclude_trash'              => true,
            'exclude_password_protected' => false,
            'exclude_nav_menu_items'     => true,

            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Also used by {@link all()}.
        $args['for_comments_only'] = (bool) $args['for_comments_only'];

        $args['include_post_ids'] = (array) $args['include_post_ids'];
        $args['exclude_post_ids'] = (array) $args['exclude_post_ids'];

        $args['include_post_types'] = (array) $args['include_post_types'];
        $args['exclude_post_types'] = (array) $args['exclude_post_types'];

        $args['include_post_statuses'] = (array) $args['include_post_statuses'];
        $args['exclude_post_statuses'] = (array) $args['exclude_post_statuses'];

        $args['exclude_drafts']             = (bool) $args['exclude_drafts'];
        $args['exclude_revisions']          = (bool) $args['exclude_revisions'];
        $args['exclude_trash']              = (bool) $args['exclude_trash'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['exclude_nav_menu_items']     = (bool) $args['exclude_nav_menu_items'];

        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Build the full SQL based on the arguments/data above.

        $sql = 'SELECT SQL_CALC_FOUND_ROWS `ID` FROM `'.esc_sql($WpDb->posts).'`'.

                ' WHERE 1=1'.// Initialize the WHERE clause in this query.

                ($args['include_post_ids'] ? ' AND `ID` IN('.$this->c::quoteSqlIn($args['include_post_ids']).')' : '').
                ($args['exclude_post_ids'] ? ' AND `ID` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_ids']).')' : '').

                ($args['include_post_types'] ? ' AND `post_type` IN('.$this->c::quoteSqlIn($args['include_post_types']).')' : '').
                ($args['exclude_post_types'] ? ' AND `post_type` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_types']).')' : '').

                ($args['include_post_statuses'] ? ' AND `post_status` IN('.$this->c::quoteSqlIn($args['include_post_statuses']).')' : '').
                ($args['exclude_post_statuses'] ? ' AND `post_status` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_statuses']).')' : '').

                ($args['exclude_drafts'] ? " AND `post_type` NOT IN('draft','auto-draft')" : '').
                ($args['exclude_revisions'] ? " AND `post_type` != 'revision'" : '').
                ($args['exclude_trash'] ? " AND `post_status` != 'trash'" : '').
                ($args['exclude_password_protected'] ? " AND `post_password` = ''" : '').
                ($args['exclude_nav_menu_items'] ? " AND `post_type` != 'nav_menu_item'" : '').

                ($args['for_comments_only'] ? " AND (`comment_status` IN('1', 'open', 'opened') OR `comment_count` > '0')" : '').

                ' LIMIT 1'; // Only one to check `FOUND_ROWS()`.

        // Run the query and return total.

        if ($WpDb->query($sql) === false) {
            throw $this->c::issue('Query failure.');
        }
        return $total = (int) $WpDb->get_var('SELECT FOUND_ROWS()');
    }

    /**
     * All posts.
     *
     * @since 160524 Post query utils.
     *
     * @param array $args Behavioral args.
     *
     * @return \WP_Post[] Array of posts.
     */
    public function all(array $args = []): array
    {
        $WpDb = $this->s::wpDb();

        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
            'for_comments_only' => false,

            'include_post_ids' => [],
            'exclude_post_ids' => [],

            'include_post_types' => [],
            'exclude_post_types' => [],

            'include_post_statuses' => [],
            'exclude_post_statuses' => [],

            'exclude_drafts'             => true,
            'exclude_revisions'          => true,
            'exclude_trash'              => true,
            'exclude_password_protected' => false,
            'exclude_nav_menu_items'     => true,

            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Also used by {@link total()}.
        $args['for_comments_only'] = (bool) $args['for_comments_only'];

        $args['include_post_ids'] = (array) $args['include_post_ids'];
        $args['exclude_post_ids'] = (array) $args['exclude_post_ids'];

        $args['include_post_types'] = (array) $args['include_post_types'];
        $args['exclude_post_types'] = (array) $args['exclude_post_types'];

        $args['include_post_statuses'] = (array) $args['include_post_statuses'];
        $args['exclude_post_statuses'] = (array) $args['exclude_post_statuses'];

        $args['exclude_drafts']             = (bool) $args['exclude_drafts'];
        $args['exclude_revisions']          = (bool) $args['exclude_revisions'];
        $args['exclude_trash']              = (bool) $args['exclude_trash'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['exclude_nav_menu_items']     = (bool) $args['exclude_nav_menu_items'];

        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($posts = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $posts; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $posts = []; // Fail; too many.
        }
        $sql = 'SELECT * FROM `'.esc_sql($WpDb->posts).'`'.

                ' WHERE 1=1'.// Initialize the WHERE clause in this query.

                ($args['include_post_ids'] ? ' AND `ID` IN('.$this->c::quoteSqlIn($args['include_post_ids']).')' : '').
                ($args['exclude_post_ids'] ? ' AND `ID` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_ids']).')' : '').

                ($args['include_post_types'] ? ' AND `post_type` IN('.$this->c::quoteSqlIn($args['include_post_types']).')' : '').
                ($args['exclude_post_types'] ? ' AND `post_type` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_types']).')' : '').

                ($args['include_post_statuses'] ? ' AND `post_status` IN('.$this->c::quoteSqlIn($args['include_post_statuses']).')' : '').
                ($args['exclude_post_statuses'] ? ' AND `post_status` NOT IN('.$this->c::quoteSqlIn($args['exclude_post_statuses']).')' : '').

                ($args['exclude_drafts'] ? " AND `post_type` NOT IN('draft','auto-draft')" : '').
                ($args['exclude_revisions'] ? " AND `post_type` != 'revision'" : '').
                ($args['exclude_trash'] ? " AND `post_status` != 'trash'" : '').
                ($args['exclude_password_protected'] ? " AND `post_password` = ''" : '').
                ($args['exclude_nav_menu_items'] ? " AND `post_type` != 'nav_menu_item'" : '').

                ($args['for_comments_only'] ? " AND (`comment_status` IN('1', 'open', 'opened') OR `comment_count` > '0')" : '').

                ' ORDER BY `post_type` ASC, `post_date_gmt` DESC'.($args['max'] !== PHP_INT_MAX ? ' LIMIT '.(int) $args['max'] : '');

        if (!($results = $WpDb->get_results($sql, OBJECT_K))) {
            return $posts = []; // No posts.
        }
        // Else we have results. Order & return array.

        $results_of_type = [ // Order of priority.
            'page'          => [],
            'post'          => [],
            'product'       => [],
            'topic'         => [],
            'reply'         => [],
            'attachment'    => [],
            'nav_menu_item' => [],
            '_other'        => [],
        ];
        // Break them down by type now.

        foreach ($results as $_key => $_result) {
            if (isset($results_of_type[$_result->post_type])) {
                $results_of_type[$_result->post_type][$_key] = $_result;
            } else {
                $results_of_type['_other'][$_key] = $_result;
            }
        } // unset($_key, $_result); // Housekeeping.

        $results = []; // In order of priority.
        foreach ($results_of_type as $_result_type => $_results) {
            $results += $_results; // Union of current + this type.
        } // unset($_result_type, $_results); // Housekeeping.

        $posts = []; // `WP_Post` instances.
        foreach ($results as $_key => $_post) {
            $posts[$_key] = new \WP_Post($_post);
        } // unset($_key, $_post); // Housekeeping.

        return $posts;
    }

    /**
     * Post select options.
     *
     * @since 160524 Post query utils.
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
            'current_post_ids'         => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'for_comments_only' => false,

            'include_post_ids' => [],
            'exclude_post_ids' => [],

            'include_post_types' => !$is_admin
                ? get_post_types(['public' => true, 'exclude_from_search' => false])
                : get_post_types(['exclude_from_search' => false]),
            'exclude_post_types' => [],

            'include_post_statuses' => !$is_admin
                ? get_post_stati(['public' => true, 'exclude_from_search' => false])
                : get_post_stati(['exclude_from_search' => false]),
            'exclude_post_statuses' => [],

            'exclude_drafts'             => true,
            'exclude_revisions'          => true,
            'exclude_trash'              => true,
            'exclude_password_protected' => !$is_admin,
            'exclude_nav_menu_items'     => true,

            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']              = (bool) $args['allow_empty'];
        $args['allow_arbitrary']          = (bool) $args['allow_arbitrary'];
        $args['option_child_indent_char'] = (string) $args['option_child_indent_char'];
        $args['option_formatter']         = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_post_ids']         = isset($args['current_post_ids']) ? (array) $args['current_post_ids'] : null;
        $args['current_post_ids']         = $this->c::removeEmptys(array_map('intval', $args['current_post_ids']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['for_comments_only'] = (bool) $args['for_comments_only'];

        $args['include_post_ids'] = (array) $args['include_post_ids'];
        $args['exclude_post_ids'] = (array) $args['exclude_post_ids'];

        $args['include_post_types'] = (array) $args['include_post_types'];
        $args['exclude_post_types'] = (array) $args['exclude_post_types'];

        $args['include_post_statuses'] = (array) $args['include_post_statuses'];
        $args['exclude_post_statuses'] = (array) $args['exclude_post_statuses'];

        $args['exclude_drafts']             = (bool) $args['exclude_drafts'];
        $args['exclude_revisions']          = (bool) $args['exclude_revisions'];
        $args['exclude_trash']              = (bool) $args['exclude_trash'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['exclude_nav_menu_items']     = (bool) $args['exclude_nav_menu_items'];

        $args['no_cache'] = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($posts = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize several working variables needed below.

        $options                 = ''; // Initialize.
        $available_post_ids      = []; // Initialize.
        $selected_post_ids       = []; // Initialize.
        $default_post_type_label = __('Post', 'wp-sharks-core');
        $default_post_title      = __('Untitled', 'wp-sharks-core');

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow `0`?
            $options = '<option value="0"></option>';
        }
        $walk = function (// Recursive parent/child walker.
            int $parent_post_id = 0,
            int $parent_depth = 0
        ) use (
            &$walk,
            &$is_admin,
            &$args,
            &$posts,
            &$options,
            &$available_post_ids,
            &$selected_post_ids,
            &$default_post_type_label,
            &$default_post_title
        ) {
            foreach ($posts as $_post) { // \WP_Post objects.
                if ((int) $_post->post_parent !== $parent_post_id) {
                    continue; // Bypass this child for now.
                }
                $available_post_ids[] = (int) $_post->ID; // Record all available.

                if (isset($args['current_post_ids']) && in_array((int) $_post->ID, $args['current_post_ids'], true)) {
                    $selected_post_ids[$_post->ID] = (int) $_post->ID; // Flag selected post ID.
                }
                $_post_type_object = get_post_type_object($_post->post_type); // Anticipate a possible failure.
                $_post_type_label  = !empty($_post_type_object->labels->singular_name) ? $_post_type_object->labels->singular_name : $default_post_type_label;

                $_post_title            = $_post->post_title ?: $default_post_title;
                $_post_date             = $this->s::dateI18nUtc('M jS, Y', strtotime($_post->post_date_gmt));
                $_post_id_selected_attr = isset($selected_post_ids[$_post->ID]) ? ' selected' : '';

                // Format `<option>` tag w/ a custom formatter?

                if ($args['option_formatter']) {
                    $options .= $args['option_formatter']($_post, [
                            'parent_post_id'        => $parent_post_id,
                            'parent_depth'          => $parent_depth,
                            'post_type_object'      => $_post_type_object,
                            'post_type_label'       => $_post_type_label,
                            'post_title'            => $_post_title,
                            'post_date'             => $_post_date,
                            'post_id_selected_attr' => $_post_id_selected_attr,
                        ], $args); // ↑ This allows for a custom option formatter.
                        // The formatter must always return an `<option></option>` tag.

                // Else format the `<option>` tag using a default behavior.
                } elseif ($is_admin) { // Slightly different format in admin area.
                    $options .= '<option value="'.esc_attr($_post->ID).'"'.$_post_id_selected_attr.'>'.
                                    ($parent_depth > 0 ? str_repeat('&nbsp;', $parent_depth).$args['option_child_indent_char'].' ' : '').
                                    esc_html($_post_type_label.' #'.$_post->ID.': '.$_post_title).
                                '</option>';
                } else { // Front-end display should be friendlier in some ways.
                    $options .= '<option value="'.esc_attr($_post->ID).'"'.$_post_id_selected_attr.'>'.
                                    ($parent_depth > 0 ? str_repeat('&nbsp;', $parent_depth).$args['option_child_indent_char'].' ' : '').
                                    esc_html($_post_date.' — '.$_post_title).
                                '</option>';
                }
                $walk((int) $_post->ID, $parent_depth + 1); // Any children this term has.
                //
            } // unset($_post, $_post_type_object, $_post_type_label, $_post_title, $_post_date, $_post_id_selected_attr); // Housekeeping.
        };
        $walk(0, 0); // Start walking/building the parent » child `<option>` tags.

        if ($args['allow_arbitrary'] && $args['current_post_ids']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_post_ids'], $available_post_ids) as $_arbitrary_post_id) {
                $options .= '<option value="'.esc_attr($_arbitrary_post_id).'" selected>'.
                                esc_html($default_post_type_label.' #'.$_arbitrary_post_id).
                            '</option>';
            } // unset($_arbitrary_post_id); // Housekeeping.
        }
        return $options; // HTML markup.
    }
}
