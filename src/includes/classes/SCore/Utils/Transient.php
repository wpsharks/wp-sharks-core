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
 * Transient utils.
 *
 * @since 160524 Transient utils.
 */
class Transient extends Classes\SCore\Base\Core
{
    /**
     * Get transient.
     *
     * @since 160524 Transient utils.
     *
     * @param string $key  Transient key.
     * @param string $hash Hashed ID.
     *
     * @return mixed|null Transient value.
     */
    public function get(string $key, string $hash = '')
    {
        if (!$key && $hash) {
            $wp_using_ext_object_cache            = wp_using_ext_object_cache(false);
            $key                                  = $this->hashToKey($hash);
            $do_restore_wp_using_ext_object_cache = true; // See below.
        }
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            $value = get_site_transient($this->App->Config->©brand['©var'].'_'.$key);
        } else {
            $value = get_transient($this->App->Config->©brand['©var'].'_'.$key);
        }
        if (!empty($do_restore_wp_using_ext_object_cache)) {
            wp_using_ext_object_cache($wp_using_ext_object_cache);
        }
        return $value === false ? null : $value;
    }

    /**
     * Set transient.
     *
     * @since 160524 Transient utils.
     *
     * @param string $key           Transient key.
     * @param mixed  $value         Transient value.
     * @param int    $expires_after Expires after X number of seconds.
     * @param bool   $db_hash       Forces a DB-driven transient & returns an encoded hash ID.
     *
     * @return string|null Returns encoded hash ID string if `$db_hash` is true.
     */
    public function set(string $key, $value, int $expires_after, bool $db_hash = false)
    {
        if ($db_hash) { // Forces DB-driven transient & returns hash ID.
            $WpDb                                 = $this->s::wpDb(); // DB class.
            $wp_using_ext_object_cache            = wp_using_ext_object_cache(false);
            $transient_key                        = $this->App->Config->©brand['©var'].'_'.$key;
            $do_restore_wp_using_ext_object_cache = true; // See below.

            if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
                set_site_transient($transient_key, $value, $expires_after);

                $sql = /* SQL to acquire site transient ID. */ '
                    SELECT `meta_id`
                        FROM `'.esc_sql($WpDb->sitemeta).'`
                    WHERE `meta_key` = %s LIMIT 1';
                $sql = $WpDb->prepare($sql, '_site_transient_'.$transient_key);

                if (!($transient_id = (int) $WpDb->get_var($sql))) {
                    throw $this->c::issue('Unable to acquire site transient ID.');
                }
            } else {
                set_transient($transient_key, $value, $expires_after);

                $sql = /* SQL to acquire transient ID. */ '
                    SELECT `option_id`
                        FROM `'.esc_sql($WpDb->options).'`
                    WHERE `option_name` = %s LIMIT 1';
                $sql = $WpDb->prepare($sql, '_transient_'.$transient_key);

                if (!($transient_id = (int) $WpDb->get_var($sql))) {
                    throw $this->c::issue('Unable to acquire transient ID.');
                }
            }
            if (!empty($do_restore_wp_using_ext_object_cache)) {
                wp_using_ext_object_cache($wp_using_ext_object_cache);
            }
            return $this->c::hashIds($transient_id); // Encoded hash ID.
            //
        } elseif ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            set_site_transient($this->App->Config->©brand['©var'].'_'.$key, $value, $expires_after);
        } else {
            set_transient($this->App->Config->©brand['©var'].'_'.$key, $value, $expires_after);
        }
    }

    /**
     * Delete transient.
     *
     * @since 160524 Transient utils.
     *
     * @param string $key  Transient key.
     * @param string $hash Hashed ID.
     */
    public function delete(string $key, string $hash = '')
    {
        if (!$key && $hash) {
            $wp_using_ext_object_cache            = wp_using_ext_object_cache(false);
            $key                                  = $this->hashToKey($hash);
            $do_restore_wp_using_ext_object_cache = true; // See below.
        }
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            delete_site_transient($this->App->Config->©brand['©var'].'_'.$key);
        } else {
            delete_transient($this->App->Config->©brand['©var'].'_'.$key);
        }
        if (!empty($do_restore_wp_using_ext_object_cache)) {
            wp_using_ext_object_cache($wp_using_ext_object_cache);
        }
    }

    /**
     * Convert hashed ID to key.
     *
     * @since 160630 Transient utils.
     *
     * @param string $hash Hashed ID.
     *
     * @return string Transient key.
     */
    protected function hashToKey(string $hash)
    {
        $blog_id = get_current_blog_id(); // Use in cache keys.

        if (($key = &$this->cacheKey(__FUNCTION__, [$blog_id, $hash])) !== null) {
            return $key; // Already cached this.
        } elseif (!($ids = $this->c::decodeHashedIds($hash))) {
            return $key = ''; // Not possible.
        } elseif (!($id = $ids[0] ?? 0)) {
            return $key = ''; // Not possible.
        }
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            $sql = 'SELECT `meta_key` FROM `'.esc_sql($WpDb->sitemeta).'` WHERE `meta_id` = %s LIMIT 1';
            $key = (string) $WpDb->get_var($WpDb->prepare($sql, $id));
        } else {
            $sql = 'SELECT `option_name` FROM `'.esc_sql($WpDb->options).'` WHERE `option_id` = %s LIMIT 1';
            $key = (string) $WpDb->get_var($WpDb->prepare($sql, $id));
        }
        return $key = preg_replace('/^_(?:site_)?transient_/u', '', $key);
    }
}
