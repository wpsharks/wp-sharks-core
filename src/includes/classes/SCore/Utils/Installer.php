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

/**
 * Install utils.
 *
 * @since 16xxxx Install utils.
 */
class Installer extends Classes\SCore\Base\Core
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
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $default_history = [
            'first_time'   => 0,
            'last_time'    => 0,
            'last_version' => '',
            'versions'     => [],
        ];
        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            if (!is_array($this->history = get_network_option(null, $this->App->Config->©brand['©var'].'_install_history'))) {
                update_network_option(null, $this->App->Config->©brand['©var'].'_install_history', $this->history = $default_history);
            }
        } elseif (!is_array($this->history = get_option($this->App->Config->©brand['©var'].'_install_history'))) {
            update_option($this->App->Config->©brand['©var'].'_install_history', $this->history = $default_history);
        }
        $this->history = array_merge($default_history, $this->history);
        $this->history = array_intersect_key($this->history, $default_history);

        foreach ($default_history as $_key => $_default_history_value) {
            settype($this->history[$_key], gettype($_default_history_value));
        } // unset($_key, $_default_history_value);
    }

    /**
     * Maybe install.
     *
     * @since 16xxxx Install utils.
     */
    public function maybeInstall()
    {
        if (!$this->history['last_version']
           || version_compare($this->history['last_version'], $this->c::version(), '<')) {
            $this->install(); // Install (or reinstall).
        }
    }

    /**
     * Install (or reinstall).
     *
     * @since 16xxxx Install utils.
     */
    protected function install()
    {
        $this->vsUpgrades();
        $this->createDbTables();

        $this->otherInstallRoutines();

        $this->flushRewriteRules();
        $this->maybeEnqueueNotice();
        $this->updateHistory();
    }

    /**
     * Version-specific upgrades.
     *
     * @since 16xxxx Install utils.
     */
    protected function vsUpgrades()
    {
        $this->s::doAction('vs_upgrades', $this->history);
    }

    /**
     * Create DB tables.
     *
     * @since 16xxxx Install utils.
     */
    protected function createDbTables()
    {
        $this->s::createDbTables();
    }

    /**
     * Other install routines.
     *
     * @since 16xxxx Install utils.
     */
    protected function otherInstallRoutines()
    {
        $this->s::doAction('other_install_routines', $this->history);
    }

    /**
     * Flush rewrite rules.
     *
     * @since 16xxxx Install utils.
     */
    protected function flushRewriteRules()
    {
        flush_rewrite_rules();
    }

    /**
     * Install (or reinstall) notice.
     *
     * @since 16xxxx Install utils.
     */
    protected function maybeEnqueueNotice()
    {
        $key = !$this->history['first_time']
            ? '§on_install' // First time?
            : '§on_reinstall';

        if ($this->App->Config->§notices[$key]) {
            if (is_callable($this->App->Config->§notices[$key])) {
                $notice = $this->App->Config->§notices[$key]($this->history);
            } else {
                $notice = $this->App->Config->§notices[$key];
            }
            if ($notice) {
                $this->s::enqueueNotice('', $notice);
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
        $time    = time();
        $version = $this->c::version();

        if (!$this->history['first_time']) {
            $this->history['first_time'] = $time;
        }
        $this->history['last_time']          = $time;
        $this->history['last_version']       = $version;
        $this->history['versions'][$version] = $time;

        uksort($this->history['versions'], 'version_compare');
        $this->history['versions'] = array_reverse($this->history['versions'], true);

        if ($this->App->Config->§specs['§is_network_wide'] && is_multisite()) {
            update_network_option(null, $this->App->Config->©brand['©var'].'_install_history', $this->history);
        } else {
            update_option($this->App->Config->©brand['©var'].'_install_history', $this->history);
        }
    }
}
