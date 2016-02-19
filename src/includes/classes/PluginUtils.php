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
 * Plugin utilities.
 *
 * @since 16xxxx Initial release.
 */
class PluginUtils extends CoreClasses\AbsCore
{
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
     * @since 16xxxx Initial release.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct();

        $this->Plugin = $Plugin;
    }

    /**
     * Magic utility factory.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $property Property.
     *
     * @return mixed Overloaded property value.
     */
    public function __get(string $property)
    {
        if (class_exists($this->Plugin->namespace.'\\Utils\\'.$property)) {
            $utility = $this->Plugin->Di->get($this->Plugin->namespace.'\\Utils\\'.$property);
            $this->overload((object) [$property => $utility], true);
            return $utility;
        } elseif (class_exists(__NAMESPACE__.'\\Utils\\Plugin\\'.$property)) {
            $utility = $this->Plugin->Di->get(__NAMESPACE__.'\\Utils\\Plugin\\'.$property);
            $this->overload((object) [$property => $utility], true);
            return $utility;
        }
        return parent::__get($property);
    }

    /**
     * Magic utility factory.
     *
     * @since 16xxxx Initial release.
     *
     * @param string $method Method to call upon.
     * @param array  $args   Arguments to pass to the method.
     *
     * @see http://php.net/manual/en/language.oop5.overloading.php
     */
    public function __call(string $method, array $args = [])
    {
        if (isset($this->¤¤overload[$method])) {
            return $this->¤¤overload[$method](...$args);
        } elseif (class_exists($this->Plugin->namespace.'\\Utils\\'.$method)) {
            $utility = $this->Plugin->Di->get($this->Plugin->namespace.'\\Utils\\'.$method);
            $this->overload((object) [$method => $utility], true);
            return $utility(...$args);
        } elseif (class_exists(__NAMESPACE__.'\\Utils\\Plugin\\'.$method)) {
            $utility = $this->Plugin->Di->get(__NAMESPACE__.'\\Utils\\Plugin\\'.$method);
            $this->overload((object) [$method => $utility], true);
            return $utility(...$args);
        }
        return parent::__call($property);
    }
}
