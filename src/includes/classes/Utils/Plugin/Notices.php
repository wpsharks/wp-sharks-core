<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin notices.
 *
 * @since 16xxxx WP notices.
 */
class Notices extends WCoreClasses\PluginBase
{
    /**
     * Enqueue an administrative notice.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $markup HTML markup containing the notice itself.
     * @param array  $args   An array of additional args; i.e., presentation/style.
     */
    public function enqueue(string $markup, array $args = [])
    {
        if (!($markup = c\mb_trim($markup))) {
            return; // Nothing to do here.
        }
        $default_args = [
            'markup' => '',

            'requires_cap' => '',

            'for_user_id' => 0,
            'for_page'    => '',

            'persistent'    => false,
            'persistent_id' => '',

            'transient'   => false,
            'push_to_top' => false,

            'type' => 'notice',
        ];
        $args['markup'] = &$markup; // + markup.

        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        $args['requires_cap'] = (string) $args['requires_cap'];

        $args['for_user_id'] = (int) $args['for_user_id'];
        $args['for_page']    = (string) $args['for_page'];

        $args['persistent']    = (bool) $args['persistent'];
        $args['persistent_id'] = (string) $args['persistent_id'];

        $args['transient']   = (bool) $args['transient'];
        $args['push_to_top'] = (bool) $args['push_to_top'];

        if (!in_array($args['type'], ['notice', 'error', 'warning'], true)) {
            $args['type'] = 'notice'; // Use default type.
        }
        ksort($args); // Sort args (by key) for key generation.
        $key = c\sha256_keyed_hash(serialize($args), $this->App->Config->plugin['notices']['key']);

        if (!is_array($notices = get_option($this->App->Config->plugin['var'].'_notices'))) {
            $notices = []; // Force an array of notices.
        }
        if ($args['push_to_top']) { // Push to top?
            c\array_unshift_assoc($notices, $key, $args);
        } else {
            $notices[$key] = $args; // Default behavior.
        }
        update_option($this->App->Config->plugin['var'].'_notices', $notices);
    }
}
