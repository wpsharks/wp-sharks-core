<?php
/**
 * Template.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\Core;

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
 * Template.
 *
 * @since 160715 Template.
 */
class Template extends CoreClasses\Core\Template
{
    /**
     * WP common.
     *
     * @since 160715
     *
     * @type Wp|null
     */
    protected $Wp;

    /**
     * Additional props.
     *
     * @since 160715 Additional props.
     */
    protected function setAdditionalProps()
    {
        if (isset($this->App->Wp)) {
            $this->Wp = &$this->App->Wp;
        }
    }
}
