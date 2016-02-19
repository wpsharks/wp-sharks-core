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
     * Notice defaults.
     *
     * @since 16xxxx WP notices.
     *
     * @type array Defaults.
     */
    protected $defaults = [];

    /**
     * Class constructor.
     *
     * @since 16xxxx Initial release.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $cap = $this->Plugin->Config->caps['view_notices'];

        $this->defaults = [
            'id'            => '',
            'type'          => 'info',
            'style'         => '',
            'markup'        => '',
            'for_user_id'   => 0,
            'for_page'      => '',
            'requires_cap'  => $cap,
            'is_persistent' => false,
            'is_transient'  => false,
            'push_to_top'   => false,
        ];
    }

    /**
     * Get notices.
     *
     * @since 16xxxx WP notices.
     *
     * @return array All notices.
     */
    public function get(): array
    {
        if (!is_array($notices = get_option($this->Plugin->Config->brand['base_var'].'_notices'))) {
            update_option($this->Plugin->Config->brand['base_var'].'_notices', $notices = []);
        }
        return $notices;
    }

    /**
     * Get notices.
     *
     * @since 16xxxx WP notices.
     *
     * @param $notices New array of notices.
     */
    public function update(array $notices)
    {
        update_option($this->Plugin->Config->brand['base_var'].'_notices', $notices);
    }

    /**
     * Enqueue an administrative notice.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $markup HTML markup containing the notice itself.
     * @param array  $args   Additional args; i.e., presentation/style.
     */
    public function enqueue(string $markup, array $args = [])
    {
        $notice           = $args;
        $notice['markup'] = &$markup; // + markup.
        $notice           = $this->normalize($notice);

        if (!$notice['markup']) {
            return; // Nothing to do.
        }
        $key     = $this->key($notice);
        $notices = $this->get();

        if ($notice['push_to_top']) {
            c\array_unshift_assoc($notices, $key, $notice);
        } else {
            $notices[$key] = $notice; // Default behavior.
        }
        $this->update($notices);
    }

    /**
     * Enqueue an administrative notice; for a particular user.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $markup HTML markup containing the notice itself.
     * @param array  $args   Additional args; i.e., presentation/style.
     */
    public function uEnqueue($markup, array $args = [])
    {
        if (!isset($args['for_user_id'])) {
            $args['for_user_id'] = get_current_user_id();
        }
        $this->enqueue($markup, $args);
    }

    /**
     * Normalize a notice.
     *
     * @since 16xxxx First documented version.
     *
     * @param array $notice Input notice.
     *
     * @return array Normalized notice.
     */
    public function normalize(array $notice): array
    {
        $notice = array_merge($this->defaults, $notice);
        $notice = array_intersect_key($notice, $this->defaults);

        $notice['id']            = (string) $notice['id'];
        $notice['type']          = (string) $notice['type'];
        $notice['style']         = (string) $notice['style'];
        $notice['markup']        = c\mb_trim((string) $notice['markup']);
        $notice['for_user_id']   = max(0, (int) $notice['for_user_id']);
        $notice['for_page']      = c\mb_trim((string) $notice['for_page']);
        $notice['requires_cap']  = c\mb_trim((string) $notice['requires_cap']);
        $notice['is_persistent'] = (bool) $notice['is_persistent'];
        $notice['is_transient']  = (bool) $notice['is_transient'];
        $notice['push_to_top']   = (bool) $notice['push_to_top'];

        if (!in_array($notice['type'], ['notice', 'error', 'warning'], true)) {
            $notice['type'] = 'notice'; // Use default type.
        }
        ksort($notice); // Sort by key.

        return $notice;
    }

    /**
     * Build a notice key.
     *
     * @since 16xxxx First documented version.
     *
     * @param array $notice Input notice.
     *
     * @return string Notice key.
     */
    public function key(array $notice): string
    {
        $serialized_notice = serialize($this->normalize($notice));

        return c\sha256_keyed_hash($serialized_notice, $this->App->Config->wp['salt']);
    }

    /**
     * Render admin notices.
     *
     * @since 16xxxx WP notices.
     *
     * @attaches-to `all_admin_notices` action.
     *
     * @see <http://jas.xyz/1Tuh3aI>
     */
    public function display()
    {
        if (!($notices = $this->get())) {
            return; // Nothing to do.
        }
        $original_notices = $notices; // Copy.

        foreach ($notices as $_key => $_notice) {
            if (!is_string($key) || !is_array($_notice)) {
                unset($notices[$_key]);
                continue; // Ignore.
            }
            $_notice = $this->normalize($_notice);

            $_current_user_can = false;
            $_class            = 'notice';
            $_style            = $_notice['style'];
            $_dismiss          = ''; // Default; n/a.

            if (!$_notice['markup']) {
                unset($notices[$_key]);
                continue; // Ignore.
            }
            if ($_notice['is_transient']) {
                unset($notices[$_key]);
            }
            if ($_notice['for_user_id'] && get_current_user_id() !== $_notice['for_user_id']) {
                continue; // Do not display.
            }
            if ($_notice['for_page'] && !wc\is_menu_page($_notice['for_page'])) {
                continue; // Do not display.
            }
            if ($_notice['requires_cap']) {
                // Pipe-delimited `|` OR logic. Can view if any are true.
                $_caps = preg_split('/\|+/u', $_notice['requires_cap']);

                foreach ($_caps as $_cap) {
                    if ($_cap && current_user_can($_cap)) {
                        $_current_user_can = true;
                        break; // Done here.
                    }
                } // Housekeeping.
                unset($_caps, $_cap);

                if (!$_current_user_can) {
                    continue; // Do not display.
                }
            }
            switch ($_notice['type']) {
                case 'info':
                    $_class .= ' notice-info';
                    break;

                case 'success':
                    $_class .= ' notice-success';
                    break;

                case 'warning':
                    $_class .= ' notice-warning';
                    break;

                case 'error':
                    $_class .= ' notice-error';
                    break;

                default: // Default behavior.
                    $_class .= ' notice-info';
            }
            if ($_notice['is_persistent']) {
                // $_class .= ' is-dismissible';
                // We use a different approach for dismissals.

                $_style .= ' padding-right:38px; position:relative;';
                $_dismiss = '<a class="notice-dismiss" href="'.esc_attr($_dismiss_url).'">'.
                                '<span class="screen-reader-text">'.__('Dismiss this notice.').'</span>'.
                            '</a>';
            }
            if (!preg_match('/^\<(?:p|div)[\s>]/ui', $_notice['markup'])) {
                $_notice['markup'] = '<p>'.$_notice['markup'].'</p>';
            }
            echo '<div class="'.esc_attr($_class).'" style="'.esc_attr($_style).'">'.
                    $_notice['markup'].$_dismiss.
                 '</div>';

            if (!$_notice['is_persistent']) {
                unset($notices[$_key]);
            }
        }
        unset($_key, $_notice, $_current_user_can, $_class, $_style, $_dismiss);

        if ($original_notices !== $notices) {
            $this->update($notices);
        }
    }
}
