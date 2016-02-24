<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
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
 * Install utils.
 *
 * @since 16xxxx Install utils.
 */
class Installer extends WCoreClasses\PluginBase
{
    /**
     * Install history.
     *
     * @since 16xxxx Install utils.
     *
     * @type array Install history.
     */
    public $history;

    /**
     * Class constructor.
     *
     * @since 16xxxx Install utils.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $Config = $this->Plugin->Config;

        $default_history = [
            'first_time'   => 0,
            'last_time'    => 0,
            'last_version' => '',
            'versions'     => [],
        ];
        if (!is_array($this->history = get_option($Config->brand['base_var'].'_install_history'))) {
            $this->history = $default_history;
        }
        $this->history = array_merge($default_history, $this->history);
        $this->history = array_intersect_key($this->history, $default_history);

        foreach ($default_history as $_key => $_default_history_value) {
            settype($this->history[$_key], gettype($_default_history_value));
        } // unset($_key, $_default_history_value);
    }

    /**
     * Install (or reinstall).
     *
     * @since 16xxxx Install utils.
     */
    public function install()
    {
        $this->vsUpgrades();
        $this->createDbTables();
        $this->enqueueNotice();
        $this->updateHistory();
    }

    /**
     * Check version.
     *
     * @since 16xxxx Install utils.
     */
    public function checkVersion()
    {
        if (defined('WP_UNINSTALL_PLUGIN')) {
            return; // Not applicable.
        }
        if (!$this->history['last_version']
           || version_compare($this->history['last_version'], $this->Plugin::VERSION, '<')) {
            $this->install(); // Install (or reinstall).
        }
    }

    /**
     * Version-specific upgrades.
     *
     * @since 16xxxx Install utils.
     */
    protected function vsUpgrades()
    {
        // Intended for extenders.
    }

    /**
     * Create DB tables.
     *
     * @since 16xxxx Install utils.
     */
    protected function createDbTables()
    {
        $this->Plugin->Utils->Db->createMissingTables();
    }

    /**
     * Install (or reinstall) notice.
     *
     * @since 16xxxx Install utils.
     */
    protected function enqueueNotice()
    {
        $Config  = $this->Plugin->Config;
        $Notices = $this->Plugin->Utils->Notices;

        if (!$this->history['first_time']) {
            if ($Config->notices['on_install']) {
                $Notices->enqueue('', $Config->notices['on_install']);
            }
        } else {
            if ($Config->notices['on_reinstall']) {
                $Notices->enqueue('', $Config->notices['on_reinstall']);
            }
        }
    }

    /**
     * Update installed version.
     *
     * @since 16xxxx Install utils.
     */
    protected function updateHistory()
    {
        $time   = time();
        $Config = $this->Plugin->Config;

        if (!$this->history['first_time']) {
            $this->history['first_time'] = $time;
        }
        $this->history['last_time']                        = $time;
        $this->history['last_version']                     = $this->Plugin::VERSION;
        $this->history['versions'][$this->Plugin::VERSION] = $time;

        update_option($Config->brand['base_var'].'_install_history', $this->history);
    }
}
