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
 * Role query utils.
 *
 * @since 16xxxx Post utils.
 */
class RolesQuery extends Classes\SCore\Base\Core
{
    /**
     * Total roles.
     *
     * @since 16xxxx Role utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total roles.
     */
    public function total(array $args = []): int
    {
        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'include'  => [],
            'exclude'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish total roles in the query.

        $roles = wp_roles()->roles; // All possible roles.
        $roles = $args['include'] ? array_intersect_key($roles, array_fill_keys($args['include'], true)) : $roles;
        $roles = $args['exclude'] ? array_diff_key($roles, array_fill_keys($args['exclude'], true)) : $roles;

        return $total = count($roles);
    }

    /**
     * All roles.
     *
     * @since 16xxxx Role utils.
     *
     * @param array $args Behavioral args.
     *
     * @return array[] Array of roles.
     */
    public function all(array $args = []): array
    {
        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
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
        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($roles = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $roles; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $roles = []; // Fail; too many.
        }
        // Return the array of all roles now.

        $roles = wp_roles()->roles; // All possible roles.
        $roles = $args['include'] ? array_intersect_key($roles, array_fill_keys($args['include'], true)) : $roles;
        $roles = $args['exclude'] ? array_diff_key($roles, array_fill_keys($args['exclude'], true)) : $roles;

        return $roles;
    }

    /**
     * Role select options.
     *
     * @since 16xxxx Role utils.
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
            'allow_empty'      => true,
            'allow_arbitrary'  => true,
            'option_formatter' => null,
            'current_roles'    => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'include'  => [],
            'exclude'  => [],
            'no_cache' => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']      = (bool) $args['allow_empty'];
        $args['allow_arbitrary']  = (bool) $args['allow_arbitrary'];
        $args['option_formatter'] = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_roles']    = isset($args['current_roles']) ? (array) $args['current_roles'] : null;
        $args['current_roles']    = $this->c::removeEmptys(array_map('strval', $args['current_roles']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['include']  = (array) $args['include'];
        $args['exclude']  = (array) $args['exclude'];
        $args['no_cache'] = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($roles = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize several working variables.

        $options         = ''; // Initialize.
        $available_roles = []; // Initialize.
        $selected_roles  = []; // Initialize.

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow ``?
            $options = '<option value=""></option>';
        }
        foreach ($roles as $_role_id => $_role) { // Array of data.
            $available_roles[] = $_role_id; // Record all available.

            if (isset($args['current_roles']) && in_array($_role_id, $args['current_roles'], true)) {
                $selected_roles[$_role_id] = $_role_id; // Flag selected role.
            }
            $_role_label         = !empty($_role['name']) ? $_role['name'] : $_role_id;
            $_role_selected_attr = isset($selected_roles[$_role_id]) ? ' selected' : '';

            // Format `<option>` tag w/ a custom formatter?

            if ($args['option_formatter']) {
                $options .= $args['option_formatter']($_role_id, [
                        'role_label'         => $_role_label,
                        'role_selected_attr' => $_role_selected_attr,
                    ], $args); // ↑ This allows for a custom option formatter.
                    // The formatter must always return an `<option></option>` tag.

            // Else format the `<option>` tag using a default behavior.
            } else { // Both front & back-end displays are the same for roles.
                $options .= '<option value="'.esc_attr($_role_id).'"'.$_role_selected_attr.'>'.
                                esc_html($_role_label).
                            '</option>';
            }
        } // unset($_role_id, $_role, $_role_label, $_role_selected_attr); // Housekeeping.

        if ($args['allow_arbitrary'] && $args['current_roles']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_roles'], $available_roles) as $_arbitrary_role_id) {
                $options .= '<option value="'.esc_attr($_arbitrary_role_id).'" selected>'.
                                esc_html($_arbitrary_role_id).
                            '</option>';
            } // unset($_arbitrary_role_id); // Housekeeping.
        }
        return $options; // HTML markup.
    }
}
