<?php
/**
 * Date utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
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
 * Date utils.
 *
 * @since 160524 WP notices.
 */
class Date extends Classes\SCore\Base\Core
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
            $format = $this->c::mbTrim($format);
        }
        $time = $time ? abs($time) : time(); // UTC time.

        // If UTC enabled, leave `$time` as-is.
        // Otherwise, convert it to a local timestamp.
        // This is necessary because `date_i18n()` doesn't do the conversion.
        // The `date_i18n()` function only works w/UTC (by default), if no `$time` is given.
        $time = $utc ? $time : $this->toLocal($time); // Adjusted time, based on `$utc` param.

        if ($utc && preg_match('/(?<!\\\\)[PIOTZe]/u', $format)) {
            // If UTC is enabled and the date format includes a TZ char.
            // Necessary, because `date_i18n()` doesn't handle this properly.
            // `date_i18n()` translates TZ chars into a local timezone regardless.

            // So here, we escape TZ chars and then apply them in UTC.
            $format = preg_replace('/(?<!\\\\)([PIOTZe])/u', '#\\\\${1}', $format);
            return preg_replace_callback('/(#)([PIOTZe])/u', function ($m) use ($time) {
                return gmdate($m[2], $time); // UTC-based TZ chars.
            }, date_i18n($format, $time, $utc));
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

    /**
     * Convert local to UTC time.
     *
     * @param int $time Timestamp (local time).
     *
     * @return int Timestamp (UTC time).
     */
    public function toUtc(int $time): int
    {
        return (int) ($time - (get_option('gmt_offset') * HOUR_IN_SECONDS));
    }

    /**
     * Convert UTC to local time.
     *
     * @param int $time Timestamp (UTC time).
     *
     * @return int Timestamp (local time).
     */
    public function toLocal(int $time): int
    {
        return (int) ($time + (get_option('gmt_offset') * HOUR_IN_SECONDS));
    }
}
