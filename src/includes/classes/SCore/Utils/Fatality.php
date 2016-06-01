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
 * Fatal error utils.
 *
 * @since 160524 Fatalities.
 */
class Fatality extends Classes\SCore\Base\Core
{
    /**
     * Forbidden response.
     *
     * @since 160524 Fatalities.
     */
    public function forbidden()
    {
        if ($this->c::isAjax()) {
            // Via JSON response.
            $this->c::statusHeader(403);
            $this->c::noCacheHeaders();
            header('content-type: application/json; charset=utf-8');

            // Standard JSON response data w/ `success` as `false`.
            die(json_encode([
                'success' => false,
                'error'   => [
                    'code'    => 403,
                    'message' => __('Abnormal request; forbidden.', 'wp-sharks-core'),
                ],
            ]));
        }
        wp_die(__('Abnormal request; forbidden.', 'wp-sharks-core'), __('Forbidden', 'wp-sharks-core'), ['response' => 403, 'back_link' => true]);
    }
}
