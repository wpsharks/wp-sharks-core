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
 * Sys option utils.
 *
 * @since 160531 Sys options.
 */
class SysOption extends Classes\SCore\Base\Core
{
    /**
     * Get/set sys version.
     *
     * @since 160531 Sys options.
     *
     * @param string     $key      Sys option key.
     * @param mixed|null $value    If setting value.
     * @param bool       $autoload Autoload option key?
     *
     * @return mixed|null Sys option value or `null`.
     */
    public function __invoke(string $key, $value = null, bool $autoload = true)
    {
        $key             = $this->App->Config->©brand['©var'].'_'.$key;
        $is_network_wide = $this->App->Config->§specs['§is_network_wide'];

        if ($is_network_wide && is_multisite()) {
            if (isset($value)) {
                update_network_option(null, $key, $value);
            }
            if (($value = get_network_option(null, $key)) === null || $value === false) {
                add_network_option(null, $key, ':null'); // Autoload impossible.
                // These will not autoload and there is no way to change this yet.
            }
        } else {
            if (isset($value)) {
                update_option($key, $value);
            }
            if (($value = get_option($key)) === null || $value === false) {
                add_option($key, ':null', '', $autoload ? 'yes' : 'no');
            }
        }
        return $value === null || $value === false || $value === ':null' ? null : $value;
    }
}
