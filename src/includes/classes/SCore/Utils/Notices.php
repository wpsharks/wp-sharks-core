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
 * Plugin notices.
 *
 * @since 160525 WP notices.
 */
class Notices extends Classes\SCore\Base\Core
{
    /**
     * Notice defaults.
     *
     * @since 160525 WP notices.
     *
     * @type array Defaults.
     */
    protected $defaults;

    /**
     * Dismiss action.
     *
     * @since 160525 WP notices.
     *
     * @type string Dismiss action.
     */
    protected $dismiss_action;

    /**
     * Class constructor.
     *
     * @since 160525 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->defaults = [
            'id'            => '',
            'type'          => 'info',
            'style'         => '',
            'markup'        => '',
            'for_user_id'   => 0,
            'for_page'      => '',
            'requires_cap'  => $this->App->Config->§caps['§manage'],
            'is_persistent' => false,
            'is_transient'  => false,
            'push_to_top'   => false,
        ];
        $this->dismiss_action = 'dismiss_'.$this->App->Config->©brand['©var'].'_notice';
    }

    /**
     * Normalize a notice.
     *
     * @since 160525 First documented version.
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
        $notice['markup']        = $this->c::mbTrim((string) $notice['markup']);
        $notice['for_user_id']   = max(0, (int) $notice['for_user_id']);
        $notice['for_page']      = $this->c::mbTrim((string) $notice['for_page']);
        $notice['requires_cap']  = $this->c::mbTrim((string) $notice['requires_cap']);
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
     * @since 160525 First documented version.
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
        return $this->c::sha256KeyedHash(serialize($notice), $this->App->Config->§keys['§salt']);
    }

    /**
     * Current user can?
     *
     * @since 160525 First documented version.
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
     * @since 160525 WP notices.
     *
     * @return array All notices.
     */
    protected function get(): array
    {
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            if (!is_array($notices = get_network_option(null, $this->App->Config->©brand['©var'].'_notices'))) {
                delete_network_option(null, $this->App->Config->©brand['©var'].'_notices');
                add_network_option(null, $this->App->Config->©brand['©var'].'_notices', $notices = []);
            }
        } elseif (!is_array($notices = get_option($this->App->Config->©brand['©var'].'_notices'))) {
            delete_option($this->App->Config->©brand['©var'].'_notices');
            add_option($this->App->Config->©brand['©var'].'_notices', $notices = [], '', 'no');
        }
        return $notices;
    }

    /**
     * Get notices.
     *
     * @since 160525 WP notices.
     *
     * @param $notices New array of notices.
     */
    protected function update(array $notices)
    {
        if ($this->get() === $notices) {
            return; // Nothing to do.
        }
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            update_network_option(null, $this->App->Config->©brand['©var'].'_notices', $notices);
        } else {
            update_option($this->App->Config->©brand['©var'].'_notices', $notices);
        }
    }

    /**
     * Enqueue an administrative notice.
     *
     * @since 160525 First documented version.
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
            $this->c::arrayUnshiftAssoc($notices, $key, $notice);
        } else {
            $notices[$key] = $notice; // Default behavior.
        }
        $this->update($notices);
    }

    /**
     * Enqueue an administrative notice; for a particular user.
     *
     * @since 160525 First documented version.
     *
     * @param string $markup HTML markup containing the notice itself.
     * @param array  $args   Additional args; i.e., presentation/style.
     */
    public function userEnqueue($markup, array $args = [])
    {
        if (!isset($args['for_user_id'])) {
            $args['for_user_id'] = get_current_user_id();
        }
        if (!$args['for_user_id']) {
            return; // Nothing to do.
        }
        $this->enqueue($markup, $args);
    }

    /**
     * Dismiss a notice.
     *
     * @since 160525 WP notices.
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
     * Dismiss URL.
     *
     * @since 160525 WP notices.
     *
     * @param string $key A key to dismiss.
     *
     * @return string Dismiss URL.
     */
    protected function dismissUrl(string $key): string
    {
        $action = $this->dismiss_action;
        $url    = $this->c::currentUrl();
        $url    = $this->c::addUrlQueryArgs([$action => $key], $url);
        $url    = $this->s::addUrlNonce($url, $action.$key);

        return $url;
    }

    /**
     * Maybe dismiss.
     *
     * @since 160525 WP notices.
     * @see <http://jas.xyz/1Tuh3aI>
     */
    public function onAdminInitMaybeDismiss()
    {
        $action = $this->dismiss_action;
        $key    = (string) ($_REQUEST[$action] ?? '');

        if (!$key || !($key = $this->c::unslash($key))) {
            return; // Nothing to do.
        }
        $this->c::noCacheHeaders();
        $this->s::requireValidNonce($action.$key);

        $notices = $this->get();

        if (isset($notices[$key])) {
            $notice = $notices[$key];

            if (!$this->currentUserCan($notice)) {
                $this->s::dieForbidden();
            }
            $this->dismiss($key);
        }
        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlNonce($url);
        $url = $this->c::removeUrlQueryArgs([$action], $url);

        wp_redirect($url);
        exit; // Stop.
    }

    /**
     * Render admin notices.
     *
     * @since 160525 WP notices.
     * @see <http://jas.xyz/1Tuh3aI>
     */
    public function onAllAdminNotices()
    {
        if (!($notices = $this->get())) {
            return; // Nothing to do.
        }
        foreach ($notices as $_key => $_notice) {
            if (!is_string($_key) || !is_array($_notice)) {
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
            if ($_notice['for_page'] && !$this->s::isMenuPage($_notice['for_page'])) {
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
                                '<span class="screen-reader-text">'.__('Dismiss this notice.', 'wp-sharks-core').'</span>'.
                            '</a>';
            }
            if (!preg_match('/^\<(?:p|div|h[1-6])[\s>]/ui', $_notice['markup'])) {
                $_notice['markup'] = '<p>'.$_notice['markup'].'</p>';
            }
            $_class = $this->c::mbTrim($_class);
            $_style = $this->c::mbTrim($_style);

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
