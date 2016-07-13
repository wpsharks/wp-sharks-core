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
 * Error utils.
 *
 * @since 160710 App utils.
 */
class Error extends Classes\SCore\Base\Core
{
    /**
     * Converts a WP_Error.
     *
     * @since 160710 App utils.
     *
     * @param \WP_Error $WP_Error Error object.
     *
     * @return CoreClasses\Core\Error Based on WP_Error
     */
    public function fromWp(\WP_Error $WP_Error): CoreClasses\Core\Error
    {
        $Error = $this->c::error();

        foreach ($WP_Error->errors as $_code => $_messages) {
            $_slug = $this->c::nameToSlug($_code);
            $_data = $WP_Error->error_data[$_code] ?? null;

            foreach ($_messages as $_message) {
                $Error->add($_slug, $_message, $_data);
            } // unset($_message); // Housekeeping.
        } // unset($_code, $_messages, $_slug, $_data); // Housekeeping.

        return $Error;
    }
}
