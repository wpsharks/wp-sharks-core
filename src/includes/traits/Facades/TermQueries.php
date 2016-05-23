<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Traits\Facades;

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

trait TermQueries
{
    /**
     * @since 16xxxx App.
     */
    public static function termsQueryTotal(...$args)
    {
        return $GLOBALS[static::class]->Utils->§TermsQuery->total(...$args);
    }

    /**
     * @since 16xxxx App.
     */
    public static function termsQueryAll(...$args)
    {
        return $GLOBALS[static::class]->Utils->§TermsQuery->all(...$args);
    }

    /**
     * @since 16xxxx Initial release.
     */
    public static function termSelectOptions(...$args)
    {
        return $GLOBALS[static::class]->Utils->§TermsQuery->selectOptions(...$args);
    }
}
