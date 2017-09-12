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
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
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
     * @param string|int $message Message (or code).
     * @param string     $slug    Custom error slug.
     * @param string     $code    Custom error status code.
     */
    public function die($message = '', string $slug = '', int $code = 500)
    {
        if (is_int($message)) {
            $code    = $message;
            $message = '';
        }
        $code    = $code ?: 500; // Default code.
        $slug    = $slug ?: $this->c::statusHeaderSlug($code);
        $message = $message ?: $this->c::statusHeaderMessage($code);

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
        } else { // Use WordPress die handler.
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
    public function dieInvalid(string $message = '', string $slug = '', int $code = 400)
    {
        $this->die($message, $slug, $code);
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
    public function dieForbidden(string $message = '', string $slug = '', int $code = 403)
    {
        $this->die($message, $slug, $code);
    }
}
