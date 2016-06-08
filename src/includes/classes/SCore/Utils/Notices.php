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
 * @since 160524 WP notices.
 */
class Notices extends Classes\SCore\Base\Core
{
    /**
     * Notice defaults.
     *
     * @since 160524 WP notices.
     *
     * @type array Defaults.
     */
    protected $defaults;

    /**
     * Class constructor.
     *
     * @since 160524 Initial release.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->defaults = [
            'id' => '',

            'type'   => 'info',
            'style'  => '',
            'markup' => '',

            'if_expr'          => '',
            'for_page'         => '',
            'not_for_page'     => '',
            'delay_until_time' => 0,

            'for_user_id' => 0,
            // Requires `§manage` by default, but apps
            // can change this behavior so care should be taken.
            'requires_cap' => $this->App->Config->§caps['§manage'],

            'is_persistent'  => false,
            'is_dismissable' => false,

            'is_transient' => false,
            'push_to_top'  => false,
        ];
    }

    /**
     * Normalize a notice.
     *
     * @since 160524 First documented version.
     *
     * @param array $notice Input notice.
     *
     * @return array Normalized notice.
     */
    protected function normalize(array $notice): array
    {
        $notice = array_merge($this->defaults, $notice);
        $notice = array_intersect_key($notice, $this->defaults);

        $notice['id'] = (string) $notice['id'];

        $notice['type']   = (string) $notice['type'];
        $notice['style']  = (string) $notice['style'];
        $notice['markup'] = $this->c::mbTrim((string) $notice['markup']);

        $notice['if_expr']          = $this->c::mbTrim((string) $notice['if_expr']);
        $notice['for_page']         = $this->c::mbTrim((string) $notice['for_page']);
        $notice['not_for_page']     = $this->c::mbTrim((string) $notice['not_for_page']);
        $notice['delay_until_time'] = max(0, (int) $notice['delay_until_time']);

        $notice['requires_cap'] = $this->c::mbTrim((string) $notice['requires_cap']);
        $notice['for_user_id']  = max(0, (int) $notice['for_user_id']);

        $notice['is_persistent']  = (bool) $notice['is_persistent'];
        $notice['is_dismissable'] = (bool) $notice['is_dismissable'];

        $notice['is_transient'] = (bool) $notice['is_transient'];
        $notice['push_to_top']  = (bool) $notice['push_to_top'];

        if ($notice['delay_until_time']) { // Sanity check.
            $notice['is_transient'] = false; // Implies NOT transient.
        }
        if (!in_array($notice['type'], ['info', 'success', 'warning', 'error'], true)) {
            $notice['type'] = 'info'; // Use default type.
        }
        ksort($notice); // Sort by key for hashing.

        return $notice;
    }

    /**
     * Build a notice key.
     *
     * @since 160524 First documented version.
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
     * @since 160524 First documented version.
     *
     * @param array $notice Input notice.
     *
     * @return bool True if current user can.
     */
    protected function currentUserCan(array $notice): bool
    {
        $notice  = $this->normalize($notice);
        $user    = wp_get_current_user();
        $user_id = (int) $user->ID;

        if ($notice['for_user_id'] && $user_id !== $notice['for_user_id']) {
            return false; // Not allowed to view notice.
        } elseif (!$notice['requires_cap']) {
            return true; // No requirements.
        }
        if (mb_strpos($notice['requires_cap'], '|') !== false) {
            foreach (preg_split('/[|\s]+/u', $notice['requires_cap']) as $_cap) {
                if ($_cap && $user->has_cap($_cap)) {
                    return true;
                }
            } // unset($_caps, $_cap);
            return false; // Unable to satisfy any.
        } else {
            foreach (preg_split('/[&,\s]+/u', $notice['requires_cap']) as $_cap) {
                if ($_cap && !$user->has_cap($_cap)) {
                    return false;
                }
            } // unset($_caps, $_cap);
            return true; // Able to satisfy all.
        }
    }

    /**
     * Get notices.
     *
     * @since 160524 WP notices.
     *
     * @return array All notices.
     */
    protected function get(): array
    {
        $notices        = $this->s::sysOption('notices', null, false);
        return $notices = is_array($notices) ? $notices : [];
    }

    /**
     * Get notices.
     *
     * @since 160524 WP notices.
     *
     * @param $notices New array of notices.
     */
    protected function update(array $notices)
    {
        if ($this->get() === $notices) {
            return; // Nothing to do.
        }
        $this->s::sysOption('notices', $notices, false);
    }

    /**
     * Enqueue an administrative notice.
     *
     * @since 160524 First documented version.
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
     * @since 160524 First documented version.
     *
     * @param string $markup HTML markup containing the notice itself.
     * @param array  $args   Additional args; i.e., presentation/style.
     */
    public function userEnqueue($markup, array $args = [])
    {
        if (!isset($args['for_user_id'])) {
            $args['for_user_id'] = (int) get_current_user_id();
        }
        if (!$args['for_user_id']) {
            return; // Nothing to do.
        }
        $this->enqueue($markup, $args);
    }

    /**
     * Dismiss a notice.
     *
     * @since 160524 WP notices.
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
     * @since 160524 WP notices.
     *
     * @param string $key A key to dismiss.
     *
     * @return string Dismiss URL.
     */
    protected function dismissUrl(string $key): string
    {
        return $this->s::addUrlRestAction('§dismiss-notice', null, $key);
    }

    /**
     * Dismiss action handler.
     *
     * @since 160524 WP notices.
     */
    public function onActionDismissNotice()
    {
        $notices = $this->get();
        $key     = (string) $this->s::restActionData();

        if ($key && isset($notices[$key])) {
            $notice = $notices[$key];

            if (!$this->currentUserCan($notice)) {
                $this->s::dieForbidden();
            }
            $this->dismiss($key);
        }
        $url = $this->c::currentUrl();
        $url = $this->s::removeUrlRestAction($url);

        wp_redirect($url);
        exit; // Stop on redirection.
    }

    /**
     * Render admin notices.
     *
     * @since 160524 WP notices.
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
            } elseif ($_notice['if_expr'] && !$this->c::phpEval($_notice['if_expr'])) {
                continue; // Do not display.
            } elseif ($_notice['for_page'] && !$this->s::isMenuPage($_notice['for_page'])) {
                continue; // Do not display.
            } elseif ($_notice['not_for_page'] && $this->s::isMenuPage($_notice['not_for_page'])) {
                continue; // Do not display.
            } elseif ($_notice['delay_until_time'] && $_notice['delay_until_time'] > time()) {
                continue; // Do not display.
            }
            switch ($_notice['type']) {
                case 'info':
                    $_class .= ' notice-info';
                    break; // Blue coloration.

                case 'success':
                    $_class .= ' notice-success';
                    break; // Green coloration.

                case 'warning':
                    $_class .= ' notice-warning';
                    break; // Orange/yellow coloration.

                case 'error':
                    $_class .= ' notice-error';
                    break; // Red coloration.

                default: // Default (info).
                    $_class .= ' notice-info';
            }
            if ($_notice['is_persistent'] && $_notice['is_dismissable']) {
                // $_class .= ' is-dismissible';
                // We use a different approach for dismissals.

                $_style .= ' padding-right:38px; position:relative;';
                $_dismiss = '<a class="notice-dismiss" style="text-decoration:none;" href="'.esc_attr($this->dismissUrl($_key)).'">'.
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
