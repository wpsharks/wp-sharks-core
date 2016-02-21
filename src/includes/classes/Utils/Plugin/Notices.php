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

        $Config = $this->Plugin->Config;

        $this->defaults = [
            'id'            => '',
            'type'          => 'info',
            'style'         => '',
            'markup'        => '',
            'for_user_id'   => 0,
            'for_page'      => '',
            'requires_cap'  => $Config->options['cap_view_notices'],
            'is_persistent' => false,
            'is_transient'  => false,
            'push_to_top'   => false,
        ];
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
    protected function normalize(array $notice): array
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
    protected function key(array $notice): string
    {
        $notice = $this->normalize($notice);

        if ($notice['id']) {
            return $notice['id']; // Use as key also.
        }
        return c\sha256_keyed_hash(serialize($notice), $this->App->Config->app['keys']['salt']);
    }

    /**
     * Current user can?
     *
     * @since 16xxxx First documented version.
     *
     * @param array $notice Input notice.
     *
     * @return bool True if current user can.
     */
    protected function currentUserCan(array $notice): bool
    {
        $notice = $this->normalize($notice);
        $user   = wp_get_current_user();

        if ($notice['for_user_id'] && $user->ID !== $notice['for_user_id']) {
            return false;
        }
        if (!$notice['requires_cap']) {
            return true;
        }
        // Pipe-delimited `|` OR logic. Can view if any are true.
        $caps = preg_split('/\|+/u', $notice['requires_cap']);

        foreach ($caps as $_cap) {
            if ($_cap && $user->has_cap($_cap)) {
                return true;
            }
        } // unset($_caps, $_cap);

        return false;
    }

    /**
     * Get notices.
     *
     * @since 16xxxx WP notices.
     *
     * @return array All notices.
     */
    protected function get(): array
    {
        $Config = $this->Plugin->Config;

        if (!is_array($notices = get_option($Config->brand['base_var'].'_notices'))) {
            delete_option($Config->brand['base_var'].'_notices');
            add_option($Config->brand['base_var'].'_notices', $notices = [], '', 'no');
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
    protected function update(array $notices)
    {
        $Config = $this->Plugin->Config;

        if ($this->get() !== $notices) {
            update_option($Config->brand['base_var'].'_notices', $notices);
        }
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
        $notice = $args; // As notice.

        // Use `$markup` if not in `$args`.
        if ($markup && empty($notice['markup'])) {
            $notice['markup'] = $markup;
        }
        $notice = $this->normalize($notice);

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
     *
     * @return mixed See {@link enqueue}.
     */
    public function uEnqueue($markup, array $args = [])
    {
        if (!isset($args['for_user_id'])) {
            $args['for_user_id'] = get_current_user_id();
        }
        return $this->enqueue($markup, $args);
    }

    /**
     * Dismiss a notice.
     *
     * @since 16xxxx WP notices.
     *
     * @param string $key A key to dismiss.
     */
    public function dismiss(string $key)
    {
        $notices = $this->get();
        unset($notices[$key]);
        $this->update($notices);
    }

    /**
     * Dismiss action.
     *
     * @since 16xxxx WP notices.
     *
     * @return string Dismiss action.
     */
    protected function dismissAction(): string
    {
        $Config = $this->Plugin->Config;

        return 'dismiss_'.$Config->brand['base_var'].'_notice';
    }

    /**
     * Dismiss URL.
     *
     * @since 16xxxx WP notices.
     *
     * @param string $key A key to dismiss.
     *
     * @return string Dismiss URL.
     */
    protected function dismissUrl(string $key): string
    {
        $url    = c\current_url();
        $action = $this->dismissAction();
        $url    = c\add_url_query_args([$action => $key], $url);
        $url    = wc\add_url_nonce($url, $action.$key);

        return $url;
    }

    /**
     * Maybe dismiss.
     *
     * @since 16xxxx WP notices.
     *
     * @attaches-to `admin_init` action.
     *
     * @see <http://jas.xyz/1Tuh3aI>
     */
    public function onAdminInitMaybeDismiss()
    {
        $action = $this->dismissAction();
        $key    = (string) ($_REQUEST[$action] ?? '');

        if (!$key || !($key = c\unslash($key))) {
            return; // Nothing to do.
        }
        nocache_headers(); // No-cache.
        wc\require_valid_nonce($action.$key);

        $notices = $this->get();

        if (isset($notices[$key])) {
            $notice = $notices[$key];

            if (!$this->currentUserCan($notice)) {
                wc\die_forbidden();
            }
            $this->dismiss($key);
        }
        $url = c\current_url();
        $url = wc\remove_url_nonce($url);
        $url = c\remove_url_query_args([$action], $url);

        wp_redirect($url);
        exit; // Stop.
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
    public function onAllAdminNotices()
    {
        if (!($notices = $this->get())) {
            return; // Nothing to do.
        }
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
            if (!$this->currentUserCan($_notice)) {
                continue; // Do not display.
            }
            if ($_notice['for_page'] && !wc\is_menu_page($_notice['for_page'])) {
                continue; // Do not display.
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
                $_dismiss = '<a class="notice-dismiss" href="'.esc_attr($this->dismissUrl($_key)).'">'.
                                '<span class="screen-reader-text">'.__('Dismiss this notice.').'</span>'.
                            '</a>';
            }
            if (!preg_match('/^\<(?:p|div|h[1-6])[\s>]/ui', $_notice['markup'])) {
                $_notice['markup'] = '<p>'.$_notice['markup'].'</p>';
            }
            echo '<div class="'.esc_attr($_class).'" style="'.esc_attr($_style).'">'.
                    $_notice['markup'].$_dismiss.
                 '</div>';

            if (!$_notice['is_persistent']) {
                unset($notices[$_key]);
            }
        } // unset($_key, $_notice, $_current_user_can, $_class, $_style, $_dismiss);

        $this->update($notices);
    }
}
