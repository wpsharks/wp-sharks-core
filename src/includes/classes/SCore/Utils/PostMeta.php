<?php
/**
 * Post meta utils.
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
 * Post meta utils.
 *
 * @since 160723 Post meta utils.
 */
class PostMeta extends Classes\SCore\Base\Core
{
    /**
     * Get prefixed meta key.
     *
     * @since 160723 Post meta utils.
     *
     * @param string $key Meta key (unprefixed).
     *
     * @return string Prefixed key.
     */
    public function key(string $key): string
    {
        // NOTE: In WordPress, keys that begin with `_`
        // are not displayed in the list of Custom Fields.

        if (!$key) { // Is the key empty?
            return ''; // Explicitly.
        }
        if (mb_strpos($key, '_') === 0) {
            $key    = mb_substr($key, 1);
            $prefix = '_'.$this->App->Config->©brand['©var'].'_';
        } else {
            $prefix = $this->App->Config->©brand['©var'].'_';
        }
        return $prefix.$key;
    }

    /**
     * Get meta value(s).
     *
     * @since 160723 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed). If empty, returns all metadata.
     * @param mixed           $default A default value when `$single` is `true` and value is `''`.
     *                                 NOTE: Do NOT set if value is allowed to be empty.
     * @param bool            $single  A single value? Default `true`. Ignored when `$key` is empty.
     *
     *                                 @internal Instead of `single=false`, use {@link collect()}.
     *
     * @return array|mixed If `!$single`, an array; else mixed.
     */
    public function get($post_id, string $key = '', $default = null, bool $single = true)
    {
        $post_id = (int) ($post_id ?? get_the_ID());
        $single  = !$key ? false : $single;

        if (!$post_id) { // Empty key OK.
            return !$single ? [] : $default;
        }
        $value = get_post_meta($post_id, $this->key($key), $single);

        if ($single && $value === '' && isset($default)) {
            return $default; // Use default value.
        }
        return $value = !$single && !is_array($value) ? [] : $value;
    }

    /**
     * Update meta value.
     *
     * @since 160723 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed).
     * @param mixed           $value   Meta value.
     * @param mixed           $where   (optional).
     */
    public function update($post_id, string $key, $value, $where = '')
    {
        $post_id = (int) ($post_id ?? get_the_ID());

        if (!$post_id || !$key) {
            return; // Not possible.
        }
        update_post_meta($post_id, $this->key($key), $value, $where);
    }

    /**
     * Delete meta value(s).
     *
     * @since 160723 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed).
     * @param mixed           $where   (optional).
     */
    public function delete($post_id, string $key, $where = '')
    {
        $post_id = (int) ($post_id ?? get_the_ID());

        if (!$post_id || !$key) {
            return; // Not possible.
        }
        delete_post_meta($post_id, $this->key($key), $where);
    }

    /**
     * Collect meta values.
     *
     * @since 160731 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed). If empty, returns all metadata.
     *
     * @return array An array of meta values.
     */
    public function collect($post_id, string $key = ''): array
    {
        $post_id = (int) ($post_id ?? get_the_ID());

        if (!$post_id) { // Empty key is OK here.
            return []; // Not possible; empty array.
        }
        $value        = get_post_meta($post_id, $this->key($key), false);
        return $value = !is_array($value) ? [] : $value;
    }

    /**
     * Set meta values.
     *
     * @since 160723 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed).
     * @param array           $values  Meta values.
     */
    public function set($post_id, string $key, array $values)
    {
        $post_id = (int) ($post_id ?? get_the_ID());

        if (!$post_id || !$key) {
            return; // Not possible.
        }
        $this->unset($post_id, $key);

        $key = $this->key($key);
        foreach ($values as $_value) {
            add_post_meta($post_id, $key, $_value);
        } // unset($_value); // Housekeeping.
    }

    /**
     * Unset meta values.
     *
     * @since 160723 Post meta utils.
     *
     * @param string|int|null $post_id Post ID; `null` = current.
     * @param string          $key     Meta key (unprefixed).
     */
    public function unset($post_id, string $key)
    {
        $post_id = (int) ($post_id ?? get_the_ID());

        if (!$post_id || !$key) {
            return; // Not possible.
        }
        delete_post_meta($post_id, $this->key($key));
    }
}
