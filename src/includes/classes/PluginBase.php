<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes;

use WebSharks\WpSharks\Core\Classes\Utils\Plugin as Utils;
#
use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Base for plugin classes.
 *
 * @since 16xxxx Initial release.
 */
abstract class PluginBase extends CoreClasses\AbsCore
{
    /**
     * App.
     *
     * @since 16xxxx
     *
     * @type App
     */
    protected $App;

    /**
     * Plugin.
     *
     * @since 16xxxx
     *
     * @type Plugin
     */
    protected $Plugin;

    /**
     * Class constructor.
     *
     * @since 15xxxx Initial release.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct();

        $this->App    = $GLOBALS[App::class];
        $this->Plugin = $Plugin;
    }
}
