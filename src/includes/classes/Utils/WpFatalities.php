<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Fatal error utils.
 *
 * @since 16xxxx WP notices.
 */
class Fatalities extends CoreClasses\AppBase
{
    /**
     * Forbidden response.
     *
     * @since 16xxxx First documented version.
     */
    public function forbidden()
    {
        wp_die(__('Abnormal request; forbidden.'), __('Forbidden'), ['response' => 403, 'back_link' => true]);
    }
}
