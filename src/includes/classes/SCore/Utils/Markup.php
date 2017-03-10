<?php
/**
 * Markup utils.
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
 * Markup utils.
 *
 * @since 17xxxx Markup utils.
 */
class Markup extends Classes\SCore\Base\Core
{
    /**
     * Errors markup.
     *
     * @since 17xxxx Markup utils.
     *
     * @param array $messages Errors.
     *
     * @return string Raw HTML markup.
     */
    public function errors(array $messages): string
    {
        if (!$messages) {
            return ''; // No error messages.
        }
        $messages      = $this->c::markdown($messages, ['no_p' => true]);
        return $markup = '<ul><li>'.implode('</li><li>', $messages).'</li></ul>';
    }
}
