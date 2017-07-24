<?php
/**
 * Fatalities.
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
 * Fatalities.
 *
 * @since 17xxxx Fatalities.
 */
class Fatalities extends Classes\SCore\Base\Core
{
    /**
     * Die (general).
     *
     * @since 17xxxx Fatalities.
     *
     * @param string $message Custom message.
     * @param string $slug    Custom error slug.
     * @param string $code    Custom error status code.
     */
    public function die(string $message = '', string $slug = '', int $code = 0)
    {
        $code    = $code ?: 503;
        $slug    = $slug ?: 'internal';
        $message = $message ?: __('Internal server error.', 'wp-sharks-core');

        $this->c::obEndCleanAll();
        $this->c::noCacheFlags();
        $this->c::noCacheHeaders();

        if ($this->c::isAjax() || $this->c::isApi()) {
            $this->c::statusHeader($code);
            header('content-type: application/json; charset=utf-8');
            exit(json_encode([
                'success' => false,
                'error'   => [
                    'code'    => $code,
                    'slug'    => $slug,
                    'message' => $message,
                ],
            ]));
        } else {
            wp_die(
                $message,
                '!', // Used as `<title>`.
                ['response' => $code, 'back_link' => true]
            );
        }
    }

    /**
     * Die (invalid).
     *
     * @since 17xxxx Fatalities.
     *
     * @param string $message Custom message.
     * @param string $slug    Custom error slug.
     * @param string $code    Custom error status code.
     */
    public function dieInvalid(string $message = '', string $slug = '', int $code = 0)
    {
        $this->die($message ?: __('Bad request.', 'wp-sharks-core'), $slug ?: 'invalid', $code ?: 400);
    }

    /**
     * Die (forbidden).
     *
     * @since 17xxxx Fatalities.
     *
     * @param string $message Custom message.
     * @param string $slug    Custom error slug.
     * @param string $code    Custom error status code.
     */
    public function dieForbidden(string $message = '', string $slug = '', int $code = 0)
    {
        $this->die($message ?: __('Forbidden.', 'wp-sharks-core'), $slug ?: 'forbidden', $code ?: 403);
    }
}
