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
 * Date utils.
 *
 * @since 16xxxx WP notices.
 */
class Date extends CoreClasses\AppBase
{
    /**
     * Date translation.
     *
     * @param string $format Date format.
     * @param int    $time   Optional timestamp (UTC always).
     * @param bool   $utc    Defaults to `false` (recommended).
     *
     * @return string Date translation (in local time, unless `$utc` is true).
     */
    public function i18n(string $format = '', int $time = 0, bool $utc = false): string
    {
        if (!$format) {
            $format = get_option('date_format');
            $format .= ' '.get_option('time_format');
            $format = c\mb_trim($format);
        }
        $time = $time ? abs($time) : time(); // Default time.
        $time = $utc ? $time : $time + (get_option('gmt_offset') * HOUR_IN_SECONDS);

        if ($utc && preg_match('/(?<!\\\\)[PIOTZe]/u', $format)) {
            $format = preg_replace('/(?<!\\\\)[PIOTZe]/u', '', $format);
            $format = c\mb_trim(preg_replace('/\s+/', ' ', $format));
            return date_i18n($format, $time, $utc).' UTC';
        }
        return date_i18n($format, $time, $utc);
    }

    /**
     * Date translation.
     *
     * @param string $format Date format.
     * @param int    $time   Optional timestamp (UTC always).
     *
     * @return string Date translation (in UTC time).
     */
    public function i18nUtc(string $format = '', int $time = 0): string
    {
        return $this->i18n($format, $time, true);
    }
}
