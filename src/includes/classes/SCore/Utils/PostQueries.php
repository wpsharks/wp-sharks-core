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
 * Post query utils.
 *
 * @since 16xxxx Post utils.
 */
class PostQueries extends Classes\SCore\Base\Core
{
    /**
     * Total posts.
     *
     * @since 16xxxx Post query utils.
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
            'for_comments_only'          => false,
            'include_post_types'         => [],
            'exclude_post_types'         => [],
            'exclude_post_statuses'      => [],
            'exclude_password_protected' => false,
            'no_cache'                   => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['for_comments_only']          = (bool) $args['for_comments_only'];
        $args['include_post_types']         = (array) $args['include_post_types'];
        $args['exclude_post_types']         = (array) $args['exclude_post_types'];
        $args['exclude_post_statuses']      = (array) $args['exclude_post_statuses'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['no_cache']                   = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish post types/statuses in the query.

        $post_types    = $args['include_post_types'] ?: get_post_types(['exclude_from_search' => false]);
        $post_statuses = get_post_stati(['exclude_from_search' => false]);

        // Build the full SQL based on the arguments/data above.

        $sql = 'SELECT SQL_CALC_FOUND_ROWS `ID` FROM `'.esc_sql($WpDb->posts).'`'.

                ' WHERE `post_type` IN('.$this->c::escSqlIn($post_types).')'.
                ($args['exclude_post_types'] ? ' AND `post_type` NOT IN('.$this->c::escSqlIn($args['exclude_post_types']).')' : '').

                ' AND `post_status` IN('.$this->c::escSqlIn($post_statuses).')'.
                ($args['exclude_post_statuses'] ? ' AND `post_status` NOT IN('.$this->c::escSqlIn($args['exclude_post_statuses']).')' : '').

                ($args['exclude_password_protected'] ? " AND `post_password` = ''" : '').

                ($args['for_comments_only'] ? " AND (`comment_status` IN('1', 'open', 'opened') OR `comment_count` > '0')" : '').

                ' LIMIT 1'; // Only one to check `FOUND_ROWS()`.

        // Run the query and return total.

        if ($WpDb->query($sql) === false) {
            throw new Exception('Query failure.');
        }
        return $total = (int) $WpDb->get_var('SELECT FOUND_ROWS()');
    }

    /**
     * All posts.
     *
     * @since 16xxxx Post query utils.
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
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Same as {@link total()}.
            'for_comments_only'          => false,
            'include_post_types'         => [],
            'exclude_post_types'         => [],
            'exclude_post_statuses'      => [],
            'exclude_password_protected' => false,
            'no_cache'                   => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        $args['for_comments_only']          = (bool) $args['for_comments_only'];
        $args['include_post_types']         = (array) $args['include_post_types'];
        $args['exclude_post_types']         = (array) $args['exclude_post_types'];
        $args['exclude_post_statuses']      = (array) $args['exclude_post_statuses'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['no_cache']                   = (bool) $args['no_cache'];

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
        // Establish post types/statuses in the query.

        $post_types    = $args['include_post_types'] ?: get_post_types(['exclude_from_search' => false]);
        $post_statuses = get_post_stati(['exclude_from_search' => false]);

        $sql = 'SELECT * FROM `'.esc_sql($WpDb->posts).'`'.

                ' WHERE `post_type` IN('.$this->c::escSqlIn($post_types).')'.
                ($args['exclude_post_types'] ? ' AND `post_type` NOT IN('.$this->c::escSqlIn($args['exclude_post_types']).')' : '').

                ' AND `post_status` IN('.$this->c::escSqlIn($post_statuses).')'.
                ($args['exclude_post_statuses'] ? ' AND `post_status` NOT IN('.$this->c::escSqlIn($args['exclude_post_statuses']).')' : '').

                ($args['exclude_password_protected'] ? " AND `post_password` = ''" : '').

                ($args['for_comments_only'] ? " AND (`comment_status` IN('1', 'open', 'opened') OR `comment_count` > '0')" : '').

                ' ORDER BY `post_type` ASC, `post_date_gmt` DESC'.($args['max'] !== PHP_INT_MAX ? ' LIMIT '.(int) $args['max'] : '');

        $post_results = $page_results = $media_results = $other_results = [];

        if (!($results = $WpDb->get_results($sql, OBJECT_K))) {
            return $posts = []; // No posts.
        }
        // Else we have results. Let's order them by type now.

        foreach ($results as $_key => $_result) {
            switch ($_result->post_type) {
                // Posts.
                case 'post':
                    $post_results[$_key] = $_result;
                    break;
                // Pages.
                case 'page':
                    $page_results[$_key] = $_result;
                    break;
                // Attachments.
                case 'attachment':
                    $media_results[$_key] = $_result;
                    break;
                // Anything else.
                default:
                    $other_results[$_key] = $_result;
                    break;
            }
        } // unset($_key, $_result); // Housekeeping.

        $results = $post_results + $page_results + $other_results + $media_results;
        $posts   = $results; // Use as posts in this order of priority.

        foreach ($posts as &$_post) {
            $_post = new \WP_Post($_post); // Convert to instance.
        } // Always unset temporary reference.
        unset($_post); // Housekeeping.

        return $posts;
    }

    /**
     * Post select options.
     *
     * @since 16xxxx Post query utils.
     *
     * @param array $args Behavioral args.
     */
    public function selectOptions(array $args = []): string
    {
        $WpDb = $this->s::wpDb();

        // In an admin area?

        $is_admin = is_admin();

        // Establish args.

        $default_args = [
            // Same as {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Same as {@link all()}.
            // Same as {@link total()}.
            'for_comments_only'          => false,
            'include_post_types'         => [],
            'exclude_post_types'         => [],
            'exclude_post_statuses'      => !$is_admin ? ['future', 'draft', 'pending', 'private'] : [],
            'exclude_password_protected' => !$is_admin,
            'no_cache'                   => false,

            'allow_empty'      => true,
            'allow_arbitrary'  => true,
            'option_formatter' => null,
            'current_post_ids' => null,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        $args['for_comments_only']          = (bool) $args['for_comments_only'];
        $args['include_post_types']         = (array) $args['include_post_types'];
        $args['exclude_post_types']         = (array) $args['exclude_post_types'];
        $args['exclude_post_statuses']      = (array) $args['exclude_post_statuses'];
        $args['exclude_password_protected'] = (bool) $args['exclude_password_protected'];
        $args['no_cache']                   = (bool) $args['no_cache'];

        $args['allow_empty']      = (bool) $args['allow_empty'];
        $args['allow_arbitrary']  = (bool) $args['allow_arbitrary'];
        $args['option_formatter'] = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_post_ids'] = isset($args['current_post_ids']) ? (array) $args['current_post_ids'] : null;

        if (!($posts = $this->all($args))) {
            return ''; // None available.
        }
        $options                 = ''; // Initialize.
        $available_post_ids      = []; // Initialize.
        $selected_post_ids       = []; // Initialize.
        $default_post_type_label = __('Post', 'wp-sharks-core');
        $default_post_title      = __('Untitled', 'wp-sharks-core');

        if ($args['allow_empty']) { // Allow `0`?
            $options = '<option value="0"></option>';
        }
        foreach ($posts as $_post) { // \WP_Post objects.
            $available_post_ids[] = $_post->ID; // Record all available.

            if (isset($args['current_post_ids']) && in_array($_post->ID, $args['current_post_ids'], true)) {
                $selected_post_ids[$_post->ID] = $_post->ID; // Flag selected post ID.
            }
            $_post_type_object = get_post_type_object($_post->post_type);
            $_post_type_label  = !empty($_post_type_object->labels->singular_name)
                ? $_post_type_object->labels->singular_name : $default_post_type_label;

            $_post_title         = $_post->post_title ?: $default_post_title;
            $_post_date          = $this->s::dateI18nUtc('M jS, Y', strtotime($_post->post_date_gmt));
            $_post_selected_attr = isset($selected_post_ids[$_post->ID]) ? ' selected' : '';

            // Format `<option>` tag w/ a custom formatter?

            if ($args['option_formatter']) {
                $options .= $args['option_formatter']($_post, [
                        'post_type_object'   => $_post_type_object,
                        'post_type_label'    => $_post_type_label,
                        'post_title'         => $_post_title,
                        'post_date'          => $_post_date,
                        'post_selected_attr' => $_post_selected_attr,
                    ], $args); // ↑ This allows for a custom option formatter.
                    // The formatter must always return an `<option></option>` tag.

            // Else format the `<option>` tag using a default behavior.
            } elseif ($is_admin) { // Slightly different format in admin area.
                $options .= '<option value="'.esc_attr($_post->ID).'"'.$_post_selected_attr.'>'.
                                esc_html($_post_type_label.' #'.$_post->ID.': '.$_post_title).
                            '</option>';
            } else { // Front-end display should be friendlier in some ways.
                $options .= '<option value="'.esc_attr($_post->ID).'"'.$_post_selected_attr.'>'.
                                esc_html($_post_date.' — '.$_post_title).
                            '</option>';
            }
        } // unset($_post, $_post_type_object, $_post_type_label, $_post_title, $_post_date, $_post_selected_attr); // Housekeeping.

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
