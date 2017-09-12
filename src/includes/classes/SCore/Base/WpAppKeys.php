<?php
/**
 * WP app keys.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;
#
use Defuse\Crypto\Key as DefuseKey;

/**
 * WP app keys.
 *
 * @since 170311.43193 WP utils.
 */
class WpAppKeys // Stand-alone class.
{
    /**
     * Class constructor.
     *
     * @since 170311.43193 Common utils.
     */
    public function __construct(Wp $Wp, array $specs, array $brand)
    {
        if ($specs['§is_network_wide'] && $Wp->is_multisite) {
            $option_hash           = sha1($Wp->initial_network_id.':'.$brand['©slug']);
            $encryption_key_option = $brand['©var'].'_encryption_key_'.$option_hash;
            $salt_key_option       = $brand['©var'].'_salt_key_'.$option_hash;

            if (!($this->encryption_key = get_network_option($Wp->initial_network_id, $encryption_key_option))) {
                add_network_option($Wp->initial_network_id, $encryption_key_option, $this->encryption_key = $this->defuseKey());
            }
            if (!($this->salt_key = get_network_option($Wp->initial_network_id, $salt_key_option))) {
                add_network_option($Wp->initial_network_id, $salt_key_option, $this->salt_key = $this->randomKey());
            }
        } else { // Standard WP, or a network running a multi-instance app (default behavior).
            $option_hash           = sha1($Wp->initial_network_id.':'.$Wp->initial_site_id.':'.$brand['©slug']);
            $encryption_key_option = $brand['©var'].'_encryption_key_'.$option_hash;
            $salt_key_option       = $brand['©var'].'_salt_key_'.$option_hash;

            if (!($this->encryption_key = get_option($encryption_key_option))) {
                add_option($encryption_key_option, $this->encryption_key = $this->defuseKey(), '', 'yes');
            }
            if (!($this->salt_key = get_option($salt_key_option))) {
                add_option($salt_key_option, $this->salt_key = $this->randomKey(), '', 'yes');
            }
        }
    }

    /**
     * Generates a Defuse key.
     *
     * @since 170311.43193 Initial release.
     *
     * @return string The Defuse key.
     */
    protected function defuseKey(): string
    {
        try { // Catch Defuse exceptions.
            if (!($key = DefuseKey::createNewRandomKey()->saveToAsciiSafeString())) {
                throw new Exception('Failed to generate an encryption key.');
            }
        } catch (\Throwable $Exception) {
            throw $Exception; // Re-throw.
        }
        return $key;
    }

    /**
     * Generates a random key.
     *
     * @since 170311.43193 Initial release.
     *
     * @param int  $char_size           Key char size. Default is `64`.
     * @param bool $special_chars       Include standard special characters? Defaults to `true`.
     * @param bool $extra_special_chars Include extra special characters? Defaults to `false`.
     *
     * @return string The random key.
     */
    protected function randomKey(int $char_size = 64, bool $special_chars = true, bool $extra_special_chars = false): string
    {
        $char_size = max(0, $char_size);

        $chars = 'abcdefghijklmnopqrstuvwxyz';
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        if ($special_chars) {
            $chars .= '!@#$%^&*()';
        }
        if ($special_chars && $extra_special_chars) {
            $chars .= '-_[]{}<>~`+=,.;:/?|';
        }
        $total_chars = mb_strlen($chars);

        for ($key = '', $_i = 0; $_i < $char_size; ++$_i) {
            $key .= mb_substr($chars, mt_rand(0, $total_chars - 1), 1);
        } // unset($_i); // Housekeeping.

        return $key;
    }
}
