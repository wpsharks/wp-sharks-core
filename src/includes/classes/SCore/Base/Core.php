<?php
/**
 * Core abstraction.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

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
 * Core abstraction.
 *
 * @since 160227 Initial release.
 */
abstract class Core extends CoreClasses\Core\Base\Core
{
    /**
     * WP common.
     *
     * @since 160524
     *
     * @var Wp
     */
    protected $Wp;

    /**
     * Class constructor.
     *
     * @since 160223 Initial release.
     *
     * @param Classes\App|null $App Instance of App.
     */
    public function __construct(Classes\App $App = null)
    {
        parent::__construct($App);

        $this->Wp = &$this->App->Wp;
    }
}
