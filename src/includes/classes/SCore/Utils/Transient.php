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
 * Transient utils.
 *
 * @since 16xxxx Transient utils.
 */
class Transient extends Classes\SCore\Base\Core
{
    /**
     * Get transient.
     *
     * @since 16xxxx Transient utils.
     *
     * @param string $key Transient key.
     *
     * @return mixed|null Transient value.
     */
    public function get(string $key)
    {
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            $value = get_site_transient($this->App->Config->©brand['©var'].'_'.$key);
        } else {
            $value = get_transient($this->App->Config->©brand['©var'].'_'.$key);
        }
        return $value === false ? null : $value;
    }

    /**
     * Set transient.
     *
     * @since 16xxxx Transient utils.
     *
     * @param string $key           Transient key.
     * @param mixed  $value         Transient value.
     * @param int    $expires_after Expires after X number of seconds.
     */
    public function set(string $key, $value, int $expires_after)
    {
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            set_site_transient($this->App->Config->©brand['©var'].'_'.$key, $value, $expires_after);
        } else {
            set_transient($this->App->Config->©brand['©var'].'_'.$key, $value, $expires_after);
        }
    }

    /**
     * Delete transient.
     *
     * @since 16xxxx Transient utils.
     *
     * @param string $key Transient key.
     */
    public function delete(string $key)
    {
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            delete_site_transient($this->App->Config->©brand['©var'].'_'.$key);
        } else {
            delete_transient($this->App->Config->©brand['©var'].'_'.$key);
        }
    }
}
