<?php
/**
 * User query utils.
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
 * User query utils.
 *
 * @since 160524 User utils.
 */
class UsersQuery extends Classes\SCore\Base\Core
{
    /**
     * Total users.
     *
     * @since 160524 User utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total users.
     */
    public function total(array $args = []): int
    {
        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'filters'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['filters']  = (array) $args['filters'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish total users in the query.

        $WP_User_Query = new \WP_User_Query(array_merge($args['filters'], [
            'fields' => 'ID', 'offset' => 0, 'paged' => 1, 'number' => 1, 'count_total' => true,
        ]));
        return $total = (int) $WP_User_Query->get_total();
    }

    /**
     * All users.
     *
     * @since 160524 User utils.
     *
     * @param array $args Behavioral args.
     *
     * @return \WP_User[] Array of users.
     */
    public function all(array $args = []): array
    {
        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
            'filters' => [
                'orderby' => 'display_name',
            ],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Also used by {@link total()}.
        $args['filters']  = (array) $args['filters'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($users = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $users; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $users = []; // Fail; too many.
        }
        // Return the array of all user objects now.

        $WP_User_Query = new \WP_User_Query(array_merge($args['filters'], [
            'fields' => 'all_with_meta', 'offset' => 0, 'paged' => 1, 'number' => -1, 'count_total' => true,
        ]));
        return $users = $WP_User_Query->get_results();
    }

    /**
     * User select options.
     *
     * @since 160524 User utils.
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
            'allow_empty'      => true,
            'allow_arbitrary'  => true,
            'option_formatter' => null,
            'current_user_ids' => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'filters' => [
                'orderby' => 'display_name',
            ],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']      = (bool) $args['allow_empty'];
        $args['allow_arbitrary']  = (bool) $args['allow_arbitrary'];
        $args['option_formatter'] = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_user_ids'] = isset($args['current_user_ids']) ? (array) $args['current_user_ids'] : null;
        $args['current_user_ids'] = $this->c::removeEmptys(array_map('intval', $args['current_user_ids']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['filters']  = (array) $args['filters'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($users = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize several working variables needed below.

        $options            = ''; // Initialize.
        $available_user_ids = []; // Initialize.
        $selected_user_ids  = []; // Initialize.
        $default_user_label = __('User', 'wp-sharks-core');

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow ``?
            $options = '<option value=""></option>';
        }
        foreach ($users as $_user) { // \WP_User objects.
            $available_user_ids[] = (int) $_user->ID; // Record all available.

            if (isset($args['current_user_ids']) && in_array((int) $_user->ID, $args['current_user_ids'], true)) {
                $selected_user_ids[$_user->ID] = (int) $_user->ID; // Flag selected user ID.
            }
            $_user_label            = $default_user_label;
            $_user_display_name     = $_user->display_name ?: $_user->user_login;
            $_user_id_selected_attr = isset($selected_user_ids[$_user->ID]) ? ' selected' : '';

            // Format `<option>` tag w/ a custom formatter?

            if ($args['option_formatter']) {
                $options .= $args['option_formatter']($_user, [
                        'user_label'            => $_user_label,
                        'user_display_name'     => $_user_display_name,
                        'user_id_selected_attr' => $_user_id_selected_attr,
                    ], $args); // ↑ This allows for a custom option formatter.
                    // The formatter must always return an `<option></option>` tag.

            // Else format the `<option>` tag using a default behavior.
            } elseif ($this->Wp->is_admin) { // Slightly different format in admin area.
                $options .= '<option value="'.esc_attr($_user->ID).'"'.$_user_id_selected_attr.'>'.
                                esc_html($_user_label.' #'.$_user->ID.': '.$_user->user_login).
                            '</option>';
            } else { // Front-end display should be friendlier in some ways.
                $options .= '<option value="'.esc_attr($_user->ID).'"'.$_user_id_selected_attr.'>'.
                                esc_html($_user->user_login.($_user_display_name !== $_user->user_login ? ' - '.$_user_display_name : '')).
                            '</option>';
            }
        } // unset($_user, $_user_label, $_user_display_name, $_user_id_selected_attr); // Housekeeping.

        if ($args['allow_arbitrary'] && $args['current_user_ids']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_user_ids'], $available_user_ids) as $_arbitrary_user_id) {
                $options .= '<option value="'.esc_attr($_arbitrary_user_id).'" selected>'.
                                esc_html($default_user_label.' #'.$_arbitrary_user_id).
                            '</option>';
            } // unset($_arbitrary_user_id); // Housekeeping.
        }
        return $options; // HTML markup.
    }
}
