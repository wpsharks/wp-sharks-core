<?php
/**
 * Endpoint utils.
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
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Endpoint utils.
 *
 * @since 17xxxx Endpoint utils.
 */
class Endpoint extends Classes\SCore\Base\Core
{
    /**
     * Is an endpoint?
     *
     * @since 17xxxx Endpoint utils.
     *
     * @param string $specific_ep Specific endpoint?
     *
     * @return bool True if endpoint, or a specific endpoint.
     */
    public function is(string $specific_ep = ''): bool
    {
        return $this->getVar($specific_ep, null) !== null;
        // e.g., `/slug/endpoint` === '' (returns true).
        // e.g., `/slug/endpoint/value` === `value` (returns true).
        // e.g., `/slug/not-an-endpoint` === `null` (returns false).
    }

    /**
     * Get endpoint query var.
     *
     * @since 17xxxx Endpoint utils.
     *
     * @param string $specific_ep Specific endpoint?
     * @note If empty, returns QV for first endpoint detected.
     *
     * @param mixed $default Default return value, passed to `get_query_var()`.
     *
     * @return mixed|null Response from `get_query_var()`, else `$default`.
     */
    public function getVar(string $specific_ep = '', $default = null)
    {
        if (!did_action('wp')) {
            throw new Exception('`wp` action not done yet.');
        }
        $WP       = $GLOBALS['wp']; // WP class instance.
        $WP_Query = $GLOBALS['wp_the_query']; // Main.

        if (!get_option('permalink_structure')) {
            return $default; // Impossible.
        } elseif (empty($WP->request) || empty($WP_Query->query_vars)) {
            return $default; // Impossible.
        } elseif (!($request_components = explode('/', $WP->request))) {
            return $default; // Impossible.
        }
        foreach ($request_components as $_maybe_ep) {
            if (!$_maybe_ep) {
                continue; // It's empty, bypass.
            } elseif ($specific_ep && $_maybe_ep !== $specific_ep) {
                continue; // Not what we're looking for.
            } elseif (!isset($WP_Query->query_vars[$_maybe_ep])) {
                continue; // It's not an endpoint; query var not set.
            } else {
                return get_query_var($_maybe_ep, $default);
            }
        } // unset($_maybe_ep); // Housekeeping.

        return $default;
    }
}
