<?php
/**
 * Notice utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
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
 * Notice utils.
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
     * @var array Defaults.
     */
    protected $defaults;

    /**
     * Outdated notice time.
     *
     * @since 160715 WP notices.
     *
     * @var int Outdated notice time.
     */
    protected $outdated_notice_time;

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
            '_insertion_time' => time(),

            'id'    => '',
            'type'  => 'info',
            'style' => '',

            'for_context'     => 'network|admin',
            'not_for_context' => '',

            'for_page'     => '',
            'not_for_page' => '',

            'recurs_every'             => 0,
            'recurs_times'             => 0,
            '_recurrences'             => 0,
            '_last_recur_dismiss_time' => 0,

            'delay_until_time' => 0,

            'for_user_id' => 0,
            // Requires `§manage` by default, but apps
            // can change this behavior so care should be taken.
            'requires_cap' => $this->App->Config->§caps['§manage'],

            'is_applicable'  => '',
            'is_persistent'  => false,
            'is_dismissable' => false,

            'is_transient' => false,
            'push_to_top'  => false,

            'markup' => '',
        ];
        $this->outdated_notice_time = strtotime('-90 days');
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

        $notice['_insertion_time'] = (int) $notice['_insertion_time'];

        $notice['id']    = (string) $notice['id'];
        $notice['type']  = (string) $notice['type'];
        $notice['style'] = (string) $notice['style'];

        $notice['for_context']     = (string) $notice['for_context'];
        $notice['not_for_context'] = (string) $notice['not_for_context'];

        $notice['for_page']     = (string) $notice['for_page'];
        $notice['not_for_page'] = (string) $notice['not_for_page'];

        $notice['recurs_every']             = max(0, (int) $notice['recurs_every']);
        $notice['recurs_times']             = max(0, (int) $notice['recurs_times']);
        $notice['_recurrences']             = max(0, (int) $notice['_recurrences']);
        $notice['_last_recur_dismiss_time'] = max(0, (int) $notice['_last_recur_dismiss_time']);

        $notice['delay_until_time'] = max(0, (int) $notice['delay_until_time']);

        $notice['requires_cap'] = (string) $notice['requires_cap'];
        $notice['for_user_id']  = max(0, (int) $notice['for_user_id']);

        if ($notice['is_applicable'] instanceof \Closure) {
            $notice['is_applicable'] = $this->c::serializeClosure($notice['is_applicable']);
        } else {
            $notice['is_applicable'] = (string) $notice['is_applicable'];
        }
        $notice['is_persistent']  = (bool) $notice['is_persistent'];
        $notice['is_dismissable'] = (bool) $notice['is_dismissable'];

        $notice['is_transient'] = (bool) $notice['is_transient'];
        $notice['push_to_top']  = (bool) $notice['push_to_top'];

        if (!$notice['id']) { // An ID is required for recurrences.
            $notice['recurs_every'] = 0; // Not possible.
        }
        if ($notice['recurs_every'] || $notice['delay_until_time']) {
            $notice['is_transient'] = false; // Implies NOT transient.
        }
        if (!in_array($notice['type'], ['info', 'success', 'warning', 'error'], true)) {
            $notice['type'] = 'info'; // Use default type.
        }
        if ($notice['markup'] instanceof \Closure) {
            $notice['markup'] = $this->c::serializeClosure($notice['markup']);
        } else {
            $notice['markup'] = (string) $notice['markup'];
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
        $hashable_notice = $this->normalize($notice);
        unset($hashable_notice['_insertion_time']);
        unset($hashable_notice['_recurrences']);
        unset($hashable_notice['_last_recur_dismiss_time']);

        if ($hashable_notice['id']) {
            return $hashable_notice['id']; // Use as key.
        }
        return $this->c::sha256KeyedHash(serialize($hashable_notice));
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
        $WP_User = wp_get_current_user();
        $notice  = $this->normalize($notice);

        if ($notice['for_user_id'] && (int) $WP_User->ID !== $notice['for_user_id']) {
            return false; // Not for current user.
        } // ↓ Everything else is related to CAP checks.

        if (!$notice['requires_cap']) {
            return true; // No CAP requirements.
        } elseif (!$WP_User->exists()) { // Not logged in?
            return false; // Not possible to satisfy anything.
        }
        if (mb_strpos($notice['requires_cap'], '&') !== false) {
            $all_required_caps = preg_split('/[&\s]+/u', $notice['requires_cap'], -1, PREG_SPLIT_NO_EMPTY);
            // e.g., `edit_posts & manage_options`. NOTE: Do not mix `&|` logic.
            // A double `&&` is also OK to use here; e.g., `edit_posts && manage_options`.

            foreach ($all_required_caps as $_cap) {
                if (!$WP_User->has_cap($_cap)) {
                    return false; // i.e., AND logic.
                }
            } // unset($_cap); // Just a little housekeeping.
            return true; // Able to satisfy all if we get here.
            //
        } else { // Default is OR logic; i.e., if any are true.
            $any_required_caps = preg_split('/[|\s]+/u', $notice['requires_cap'], -1, PREG_SPLIT_NO_EMPTY);
            // e.g., `edit_posts|manage_options`. NOTE: Do not mix `&|` logic.
            // A double `||` is also OK to use here; e.g., `edit_posts || manage_options`.

            foreach ($any_required_caps as $_cap) {
                if ($WP_User->has_cap($_cap)) {
                    return true; // i.e., OR logic.
                }
            } // unset($_cap); // Just a little housekeeping.
            return false; // Unable to satisfy any if we get here.
        }
    }

    /**
     * Is an applicable context?
     *
     * @since 160715 Notice contexts.
     *
     * @param array $notice Input notice.
     *
     * @return bool True if an applicable context.
     */
    protected function isApplicableContext(array $notice): bool
    {
        if ($this->Wp->is_network_admin) {
            $context = 'network';
        } elseif ($this->Wp->is_user_admin) {
            $context = 'user';
        } elseif ($this->Wp->is_admin) {
            $context = 'admin';
        } else { // Should not happen, but just in case.
            debug(0, $this->c::issue(vars(), 'Context unknown.'));
            return false; // Unknown w/ debugger.
        }
        $notice                  = $this->normalize($notice);
        $applicable_contexts     = preg_split('/[|\s]+/u', $notice['for_context'], -1, PREG_SPLIT_NO_EMPTY);
        $not_applicable_contexts = preg_split('/[|\s]+/u', $notice['not_for_context'], -1, PREG_SPLIT_NO_EMPTY);
        // e.g., `network|admin`, `network || admin`, or `network admin`.

        if ($applicable_contexts && !in_array($context, $applicable_contexts, true)) {
            return false; // Not applicable in this context.
        } elseif ($not_applicable_contexts && in_array($context, $not_applicable_contexts, true)) {
            return false; // Not applicable in this context.
        }
        return true; // Applicable context (default behavior).
    }

    /**
     * Is an applicable page?
     *
     * @since 160715 Notice contexts.
     *
     * @param array $notice Input notice.
     *
     * @return bool True if an applicable page.
     */
    protected function isApplicablePage(array $notice): bool
    {
        $notice = $this->normalize($notice);

        if ($notice['for_page'] && $notice['for_page'][0] === '/') {
            // If it begins with a `/`, treat it as pure regex like `isMenuPage()` does.
            $applicable_pages = [$notice['for_page']]; // Delimiters not supported here.
        } else { // May contain WRegx, but `|` is not a reserved char, so this is OK.
            $applicable_pages = preg_split('/[|\s]+/u', $notice['for_page'], -1, PREG_SPLIT_NO_EMPTY);
            // e.g., `index.php|post.php`, `page-slug{-*,} || another-page-slug`, or `slug1{-*,} slug2`.
        }
        if ($notice['not_for_page'] && $notice['not_for_page'][0] === '/') {
            // If it begins with a `/`, treat it as pure regex like `isMenuPage()` does.
            $not_applicable_pages = [$notice['not_for_page']]; // Delimiters not supported here.
        } else { // May contain WRegx, but `|` is not a reserved char, so this is OK.
            $not_applicable_pages = preg_split('/[|\s]+/u', $notice['not_for_page'], -1, PREG_SPLIT_NO_EMPTY);
            // e.g., `index.php|post.php`, `page-slug{-*,} || another-page-slug`, or `slug1{-*,} slug2`.
        }
        foreach ($applicable_pages as $_applicable_page) {
            if ($this->s::isMenuPage($_applicable_page)) {
                $is_an_applicable_page = true;
                break; // Have what's needed here.
            } // In short, if any match current page.
        } // unset($_applicable_page); // Housekeeping.

        if ($applicable_pages && empty($is_an_applicable_page)) {
            return false; // Not an applicable page.
        }
        foreach ($not_applicable_pages as $_not_applicable_page) {
            if ($this->s::isMenuPage($_not_applicable_page)) {
                return false; // Not applicable.
            } // In short, if any match current page.
        } // unset($_not_applicable_page); // Housekeeping.

        return true; // Applicable page (default behavior).
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
     * @param string|\Closure $markup HTML (or closure) containing the notice.
     * @param array           $args   Additional args; i.e., presentation/style.
     */
    public function enqueue($markup, array $args = [])
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
     * @param string|\Closure $markup HTML (or closure) containing the notice.
     * @param array           $args   Additional args; i.e., presentation/style.
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
     * Dequeue a notice.
     *
     * @since 161014 WP notices.
     *
     * @param string $key A key to dequeue.
     */
    public function dequeue(string $key)
    {
        $notices = $this->get();

        if (!isset($notices[$key])) {
            return; // Nothing to do.
        } // No update necessary in this case.

        unset($notices[$key]); // Dequeue.
        $this->update($notices);
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

        if (!isset($notices[$key])) {
            return; // Nothing to do.
        } // No update necessary in this case.

        $notices[$key] = $this->normalize($notices[$key]);
        $notice        = &$notices[$key]; // By reference.

        if ($notice['recurs_every']) {
            ++$notice['_recurrences']; // Counter.
            // A recurring + persistent notice is counted/updated when it's dismissed.
            // i.e., Last recur time set to the time in which it is dismissed by a user.

            if ($notice['_recurrences'] < $notice['recurs_times']) {
                $notice['_last_recur_dismiss_time'] = time();
            } else {
                unset($notices[$key]); // Dequeue.
            }
        } else {
            unset($notices[$key]); // Dequeue.
        }
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
    public function dismissUrl(string $key): string
    {
        return $this->s::restActionUrl('§dismiss-notice', $key);
    }

    /**
     * Dismiss action handler.
     *
     * @since 160524 WP notices.
     */
    public function onRestActionDismissNotice()
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

        wp_redirect($url).exit(); // Stop on redirection.
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
        $time = time(); // The current time.

        foreach ($notices as $_key => &$_notice) {
            # Catch invalid/corrupted notices.

            if (!is_string($_key) || !is_array($_notice)) {
                unset($notices[$_key]);
                continue; // Ignore.
            }
            # Normalize the notice.

            $_notice = $this->normalize($_notice);

            # Initialize a few variables.

            $_markup           = '';
            $_current_user_can = false;
            $_is_applicable    = false;

            $_class = $this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
            $_class .= ' -notice';
            $_class .= ' notice';

            $_style   = $_notice['style'];
            $_dismiss = ''; // Default; n/a.

            # Check for empty markup.

            if (!($_markup = $_notice['markup'])) {
                unset($notices[$_key]);
                continue; // Ignore.
            }
            # If transient, wipe after a single pass.

            if ($_notice['is_transient']) {
                unset($notices[$_key]);
            }
            # Has notice become too old; i.e., lingering?

            if ($_notice['_insertion_time'] < $this->outdated_notice_time
                && /* Not intentially delayed. */ $_notice['delay_until_time'] < $time) {
                unset($notices[$_key]); // Ancient history after this pass.
            }
            # Check conditions; i.e., is notice applicable?

            if (!$this->currentUserCan($_notice)) {
                continue; // Do not display.
            } elseif (!$this->isApplicableContext($_notice)) {
                continue; // Do not display.
            } elseif (!$this->isApplicablePage($_notice)) {
                continue; // Do not display.
            } elseif ($_notice['delay_until_time'] > $time) {
                continue; // Do not display.
            } elseif ($_notice['recurs_every'] && $_notice['_last_recur_dismiss_time']
                && $_notice['_last_recur_dismiss_time'] + $_notice['recurs_every'] > $time) {
                continue; // Do not display; not time to recur yet, based on last time.
            }
            # If `is_applicable` is a closure, check it's return value also.

            if ($this->c::isSerialized($_notice['is_applicable'])) {
                try { // Maybe catch exceptions here.

                    $_is_applicable = $this->c::unserializeClosure($_notice['is_applicable']);
                    $_is_applicable = $_is_applicable($this->App); // Should return (bool) or `null`.
                    $_is_applicable = $_is_applicable === null ? $_is_applicable : (bool) $_is_applicable;

                    // NOTE: A special return value of `null` indicates the notice
                    // is no longer applicable (at all) and should be dequeued entirely.

                    if ($_is_applicable === null) {
                        unset($notices[$_key]);
                        continue; // Ignore.
                    } elseif (!$_is_applicable) {
                        continue; // Do not display.
                    }
                } catch (\Throwable $Throwable) {
                    unset($notices[$_key]); // Avoid repeats.

                    if ($this->App->Config->©debug['©enable']) {
                        throw $Throwable;
                    }
                    continue; // Ignore.
                }
            }
            # If `markup` is a closure, call upon the closure now.

            if ($this->c::isSerialized($_notice['markup'])) {
                try { // Maybe catch exceptions here.

                    $_markup = $this->c::unserializeClosure($_notice['markup']);
                    $_markup = (string) $_markup($this->App); // Should return a string.

                    // NOTE: A special return value (empty) indicates the notice
                    // is no longer applicable (at all) and should be dequeued entirely.

                    if (!$_markup) {
                        unset($notices[$_key]);
                        continue; // Ignore.
                    }
                } catch (\Throwable $Throwable) {
                    unset($notices[$_key]); // Avoid repeats.

                    if ($this->App->Config->©debug['©enable']) {
                        throw $Throwable;
                    }
                    continue; // Ignore.
                }
            }
            # Setup notice classes for CSS in WP core.

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
            // Create dismiss icon if applicable.

            if ($_notice['is_persistent'] && $_notice['is_dismissable']) {
                // $_class .= ' is-dismissible';
                // We use a different approach for dismissals.

                $_style .= ' padding-right:38px; position:relative;';
                $_dismiss = '<a class="notice-dismiss" style="text-decoration:none;" href="'.esc_attr($this->dismissUrl($_key)).'">'.
                                '<span class="screen-reader-text">'.__('Dismiss this notice.', 'wp-sharks-core').'</span>'.
                            '</a>';
            }
            # Make sure markup is wrapped in a block-level tag so margins will exist.

            $_markup = $this->c::mbTrim($_markup); // Before checking.
            if (!preg_match('/^\<(?:p|div|form|h[1-6]|ul|ol)[\s>]/ui', $_markup)) {
                $_markup = '<p>'.$_markup.'</p>'; // Add `<p>` tag.
            }
            # Display notice `<div>` with the markup and a possible dismiss icon.

            echo '<div class="'.esc_attr($_class).'" style="'.esc_attr($_style).'">'.
                    $_markup.$_dismiss.// Possible dismiss icon.
                 '</div>';

            # Notice seen. Handle queue state logic now.

            if ($_notice['is_persistent']) {
                continue; // Persistent; do nothing.
                // See {@link dismiss()} for recurring + persistent notice handling.
                // A recurring + persistent notice is counted/updated when it's dismissed.
                // If it's not dismissable, then it simply remains until dequeued manually.
            } elseif ($_notice['recurs_every']) {
                ++$_notice['_recurrences']; // Update counter.

                if ($_notice['_recurrences'] < $_notice['recurs_times']) {
                    $_notice['_last_recur_dismiss_time'] = $time;
                } else {
                    unset($notices[$_key]); // Dequeue.
                }
            } else {
                unset($notices[$_key]); // Dequeue.
            }
        } // unset($_key, $_notice, $_markup, $_current_user_can, $_is_applicable, $_class, $_style, $_dismiss);

        $this->update($notices);
    }
}
