<?php
/**
 * Cap query utils.
 *
 * @author @jaswrks
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
 * Cap query utils.
 *
 * @since 160524 Post utils.
 */
class CapsQuery extends Classes\SCore\Base\Core
{
    /**
     * Deprecated level caps.
     *
     * @since 160524 Cap utils.
     *
     * @var string
     */
    protected $deprecated_levels;

    /**
     * Class constructor.
     *
     * @since 160524 Cap utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        for ($_i = 0; $_i <= 10; ++$_i) {
            $this->deprecated_levels[] = 'level_'.$_i;
        }
    }

    /**
     * Total caps.
     *
     * @since 160524 Cap utils.
     *
     * @param array $args Behavioral args.
     *
     * @return int Total caps.
     */
    public function total(array $args = []): int
    {
        // Establish args.

        $default_args = [
            // Also used by {@link all()}.
            'include'                   => [],
            'exclude'                   => [],
            'exclude_deprecated_levels' => true,
            'no_cache'                  => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['include']                   = (array) $args['include'];
        $args['exclude']                   = (array) $args['exclude'];
        $args['exclude_deprecated_levels'] = (bool) $args['exclude_deprecated_levels'];
        $args['no_cache']                  = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($total = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $total; // Already cached this.
        }
        // Establish total caps in the query.

        $caps = $this->collectAll(); // All possible caps.

        $caps = $args['include'] ? array_intersect_key($caps, array_fill_keys($args['include'], true)) : $caps;
        $caps = $args['exclude'] ? array_diff_key($caps, array_fill_keys($args['exclude'], true)) : $caps;
        $caps = $args['exclude_deprecated_levels'] ? array_diff_key($caps, array_fill_keys($this->deprecated_levels, true)) : $caps;

        return $total = count($caps);
    }

    /**
     * All caps.
     *
     * @since 160524 Cap utils.
     *
     * @param array $args Behavioral args.
     *
     * @return string[] Array of caps.
     */
    public function all(array $args = []): array
    {
        // Establish args.

        $default_args = [
            // Unique.
            'max'         => PHP_INT_MAX,
            'fail_on_max' => false,

            // Also used by {@link total()}.
            'include'                   => [],
            'exclude'                   => [],
            'exclude_deprecated_levels' => true,
            'no_cache'                  => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Also used by {@link total()}.
        $args['include']                   = (array) $args['include'];
        $args['exclude']                   = (array) $args['exclude'];
        $args['exclude_deprecated_levels'] = (bool) $args['exclude_deprecated_levels'];
        $args['no_cache']                  = (bool) $args['no_cache'];

        // Check cache; already did this query?

        $cache_keys = $args; // Keys to consider when checking the cache.
        unset($cache_keys['no_cache']); // Cache key exclusions.

        if (($caps = &$this->cacheKey(__FUNCTION__, $cache_keys)) !== null && !$args['no_cache']) {
            return $caps; // Already cached this.
        }
        // Automatically fail if there are too many; when/if desirable.

        if ($args['fail_on_max'] && $this->total($args) > $args['max']) {
            return $caps = []; // Fail; too many.
        }
        // Return the array of all caps now.

        $caps = $this->collectAll(); // All possible caps.

        $caps = $args['include'] ? array_intersect_key($caps, array_fill_keys($args['include'], true)) : $caps;
        $caps = $args['exclude'] ? array_diff_key($caps, array_fill_keys($args['exclude'], true)) : $caps;
        $caps = $args['exclude_deprecated_levels'] ? array_diff_key($caps, array_fill_keys($this->deprecated_levels, true)) : $caps;

        asort($caps, SORT_NATURAL); // Sort naturally.

        return $caps;
    }

    /**
     * Cap select options.
     *
     * @since 160524 Cap utils.
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
            'current_caps'     => null,

            // Used by {@link all()}.
            'max'         => 1000,
            'fail_on_max' => true,

            // Used by {@link total()}.
            // Used by {@link all()}.
            'include'                   => [],
            'exclude'                   => [],
            'exclude_deprecated_levels' => true,
            'no_cache'                  => false,
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        // Unique.
        $args['allow_empty']      = (bool) $args['allow_empty'];
        $args['allow_arbitrary']  = (bool) $args['allow_arbitrary'];
        $args['option_formatter'] = is_callable($args['option_formatter']) ? $args['option_formatter'] : null;
        $args['current_caps']     = isset($args['current_caps']) ? (array) $args['current_caps'] : null;
        $args['current_caps']     = $this->c::removeEmptys(array_map('strval', $args['current_caps']));

        // Used by {@link all()}.
        $args['max']         = max(1, (int) $args['max']);
        $args['fail_on_max'] = (bool) $args['fail_on_max'];

        // Used by {@link total()}.
        // Used by {@link all()}.
        $args['include']                   = (array) $args['include'];
        $args['exclude']                   = (array) $args['exclude'];
        $args['exclude_deprecated_levels'] = (bool) $args['exclude_deprecated_levels'];
        $args['no_cache']                  = (bool) $args['no_cache'];

        // Check for nothing being available (or too many).

        if (!($caps = $this->all($args))) {
            return ''; // None available.
        }
        // Initialize several working variables.

        $options        = ''; // Initialize.
        $available_caps = []; // Initialize.
        $selected_caps  = []; // Initialize.

        // Build & return all `<option>` tags.

        if ($args['allow_empty']) { // Allow ``?
            $options = '<option value=""></option>';
        }
        foreach ($caps as $_cap) { // Key/value the same at this time.
            $available_caps[] = $_cap; // Record all available.

            if (isset($args['current_caps']) && in_array($_cap, $args['current_caps'], true)) {
                $selected_caps[$_cap] = $_cap; // Flag selected cap.
            }
            $_cap_selected_attr = isset($selected_caps[$_cap]) ? ' selected' : '';

            // Format `<option>` tag w/ a custom formatter?

            if ($args['option_formatter']) {
                $options .= $args['option_formatter']($_cap, [
                        'cap_selected_attr' => $_cap_selected_attr,
                    ], $args); // ↑ This allows for a custom option formatter.
                    // The formatter must always return an `<option></option>` tag.

            // Else format the `<option>` tag using a default behavior.
            } else { // Both front & back-end displays are the same for caps.
                $options .= '<option value="'.esc_attr($_cap).'"'.$_cap_selected_attr.'>'.
                                esc_html($_cap).
                            '</option>';
            }
        } // unset($_cap, $_cap_selected_attr); // Housekeeping.

        if ($args['allow_arbitrary'] && $args['current_caps']) { // Allow arbitrary select `<option>`s?
            foreach (array_diff($args['current_caps'], $available_caps) as $_arbitrary_cap) {
                $options .= '<option value="'.esc_attr($_arbitrary_cap).'" selected>'.
                                esc_html($_arbitrary_cap).
                            '</option>';
            } // unset($_arbitrary_cap); // Housekeeping.
        }
        return $options; // HTML markup.
    }

    /**
     * Collect role caps.
     *
     * @since 160524 Cap utils.
     *
     * @param string $role_id  Role ID.
     * @param bool   $no_cache Bypass cache?
     *
     * @return array All role caps.
     */
    public function forRole(string $role_id, bool $no_cache = false): array
    {
        if (($collection = &$this->cacheKey(__FUNCTION__, $role_id)) !== null && !$no_cache) {
            return $collection; // Already cached this.
        }
        $collection = []; // Initialize.

        if ($role_id === 'super_admin') {
            $role_id = 'administrator';

            if ($this->Wp->is_multisite) {
                $collection = array_merge($collection, [
                    'manage_network'         => 'manage_network',
                    'manage_sites'           => 'manage_sites',
                    'manage_network_users'   => 'manage_network_users',
                    'manage_network_plugins' => 'manage_network_plugins',
                    'manage_network_themes'  => 'manage_network_themes',
                    'manage_network_options' => 'manage_network_options',
                ]);
            }
        }
        $role = get_role($role_id); // If role exists.

        foreach (array_keys($role->capabilities ?? []) as $_role_cap) {
            $collection[$_role_cap] = $_role_cap;
        } // unset($_role_cap); // Housekeeping.

        asort($collection, SORT_NATURAL);

        return $collection;
    }

    /**
     * Collect caps.
     *
     * @since 160524 Cap utils.
     *
     * @param bool $no_cache Bypass cache?
     *
     * @return array All collected caps.
     */
    public function collectAll(bool $no_cache = false): array
    {
        if (($collection = &$this->cacheKey(__FUNCTION__)) !== null && !$no_cache) {
            return $collection; // Already cached this.
        }
        $collection = []; // Initialize.

        foreach (wp_roles()->roles as $_role_id => $_role) {
            foreach (array_keys($_role['capabilities'] ?? []) as $_role_cap) {
                $collection[$_role_cap] = $_role_cap;
            }
        } // unset($_role_id, $_role, $_role_cap); // Housekeeping.

        $collection = array_merge($collection, $this->forRole('super_admin', $no_cache));

        foreach (get_post_types([], 'objects') as $_post_type => $_post_type_object) {
            foreach ($_post_type_object->cap ?? [] as $_core_cap => $_post_type_cap) {
                if (!in_array($_core_cap, ['read_post', 'edit_post', 'delete_post'], true)) {
                    // ↑ Do not include post meta caps; see: <http://jas.xyz/1XN7IKd>
                    $collection[$_core_cap]      = $_core_cap;
                    $collection[$_post_type_cap] = $_post_type_cap;
                }
            } // unset($_core_cap, $_post_type_cap);
        } // unset($_post_type, $_post_type_object); // Housekeeping.

        foreach (get_taxonomies([], 'objects') as $_taxonomy => $_taxonomy_object) {
            foreach ($_taxonomy_object->cap ?? [] as $_core_cap => $_taxonomy_cap) {
                $collection[$_core_cap]     = $_core_cap;
                $collection[$_taxonomy_cap] = $_taxonomy_cap;
            } // unset($_core_cap, $_taxonomy_cap);
        } // unset($_taxonomy, $_taxonomy_object); // Housekeeping.

        asort($collection, SORT_NATURAL);

        return $collection;
    }
}
