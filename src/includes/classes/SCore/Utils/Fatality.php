<?php
/**
 * Fatal error utils.
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
 * Fatal error utils.
 *
 * @since 160524 Fatalities.
 */
class Fatality extends Classes\SCore\Base\Core
{
    /**
     * Invalid.
     *
     * @since 160606 Fatalities.
     *
     * @param string $message Custom message.
     * @param string $slug    Custom error slug.
     * @param string $code    Custom error status code.
     */
    public function invalid(string $message = '', string $slug = '', int $code = 0)
    {
        $code    = $code ?: 400;
        $slug    = $slug ?: 'invalid';
        $message = $message ?: __('Bad request.', 'wp-sharks-core');

        if ($this->c::isAjax() || $this->c::isApi()) {
            // Via JSON response.
            $this->c::statusHeader($code);
            $this->c::noCacheFlags();
            $this->c::noCacheHeaders();
            header('content-type: application/json; charset=utf-8');

            // Standard JSON response data w/ `success` as `false`.
            exit(json_encode([
                'success' => false,
                'error'   => [
                    'code'    => $code,
                    'slug'    => $slug,
                    'message' => $message,
                ],
            ]));
        }
        wp_die($message, __('Bad request.', 'wp-sharks-core'), ['response' => $code, 'back_link' => true]);
    }

    /**
     * Forbidden.
     *
     * @since 160524 Fatalities.
     *
     * @param string $message Custom message.
     * @param string $slug    Custom error slug.
     * @param string $code    Custom error status code.
     */
    public function forbidden(string $message = '', string $slug = '', int $code = 0)
    {
        $code    = $code ?: 403;
        $slug    = $slug ?: 'forbidden';
        $message = $message ?: __('Forbidden.', 'wp-sharks-core');

        if ($this->c::isAjax() || $this->c::isApi()) {
            // Via JSON response.
            $this->c::statusHeader($code);
            $this->c::noCacheFlags();
            $this->c::noCacheHeaders();
            header('content-type: application/json; charset=utf-8');

            // Standard JSON response data w/ `success` as `false`.
            exit(json_encode([
                'success' => false,
                'error'   => [
                    'code'    => $code,
                    'slug'    => $slug,
                    'message' => $message,
                ],
            ]));
        }
        wp_die($message, __('Forbidden', 'wp-sharks-core'), ['response' => $code, 'back_link' => true]);
    }
}
