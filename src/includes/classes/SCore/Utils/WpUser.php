<?php
/**
 * WP user utils.
 *
 * @author @jaswrks
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
 * WP user utils.
 *
 * @since 17xxxx WP user utils.
 */
class WpUser extends Classes\SCore\Base\Core
{
    /**
     * Log in as a specific WP user.
     *
     * @since 17xxxx WP user utils.
     *
     * @param int  $user_id  User ID.
     * @param bool $remember Remember them?
     *
     * @return bool True if logged-in.
     * @note This updates cookies in real-time.
     */
    public function loginAs(int $user_id, bool $remember = false): bool
    {
        if (!$user_id) {
            return false; // Impossible.
        }
        $on_set_auth_cookie = function (string $value) {
            $_COOKIE[is_ssl() ? SECURE_AUTH_COOKIE : AUTH_COOKIE] = $value;
        };
        $on_set_logged_in_cookie = function (string $value) {
            $_COOKIE[LOGGED_IN_COOKIE] = $value;
        };
        add_action('set_auth_cookie', $on_set_auth_cookie);
        add_action('set_logged_in_cookie', $on_set_logged_in_cookie);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($user_id, $remember);

        remove_action('set_auth_cookie', $on_set_auth_cookie);
        remove_action('set_logged_in_cookie', $on_set_logged_in_cookie);

        wp_set_current_user(0);
        wp_set_current_user($user_id);

        return (int) get_current_user_id() === $user_id;
    }

    /**
     * Is current user?
     *
     * @since 17xxxx WP user utils.
     *
     * @param int $user_id User ID.
     *
     * @return bool True if current user.
     */
    public function isCurrent(int $user_id): bool
    {
        return $user_id && $user_id === (int) get_current_user_id();
    }
}
