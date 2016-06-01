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
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Install utils.
 *
 * @since 160524 Install utils.
 */
class Installer extends Classes\SCore\Base\Core
{
    /**
     * Install history.
     *
     * @since 160524 Install utils.
     *
     * @type array Install history.
     */
    protected $history;

    /**
     * Class constructor.
     *
     * @since 160524 Install utils.
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
        if (!is_array($this->history = $this->s::sysOption('install_history'))) {
            $this->history = $default_history; // Defaults.
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
     * @since 160524 Install utils.
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
     * @since 160524 Install utils.
     */
    protected function install()
    {
        $this->vsUpgrades();
        $this->createDbTables();

        $this->otherInstallRoutines();

        $this->doFlushRewriteRules();
        $this->maybeEnqueueNotices();
        $this->updateHistory();
    }

    /**
     * Version-specific upgrades.
     *
     * @since 160524 Install utils.
     */
    protected function vsUpgrades()
    {
        $this->s::doAction('vs_upgrades', $this->history);
    }

    /**
     * Create DB tables.
     *
     * @since 160524 Install utils.
     */
    protected function createDbTables()
    {
        $this->s::createDbTables();
    }

    /**
     * Other install routines.
     *
     * @since 160524 Install utils.
     */
    protected function otherInstallRoutines()
    {
        $this->s::doAction('other_install_routines', $this->history);
    }

    /**
     * Flush rewrite rules.
     *
     * @since 160524 Install utils.
     */
    protected function doFlushRewriteRules()
    {
        if (!empty($GLOBALS['wp_rewrite'])) {
            flush_rewrite_rules();
        } else {
            add_action('setup_theme', 'flush_rewrite_rules', -10000);
        }
    }

    /**
     * Install (or reinstall) notices.
     *
     * @since 160524 Install utils.
     */
    protected function maybeEnqueueNotices()
    {
        if (!$this->history['first_time']) {
            $template_file = 's-core/notices/on-install.php';
        } else {
            $template_file = 's-core/notices/on-reinstall.php';
        }
        $Template      = $this->c::getTemplate($template_file);
        $notice_markup = $Template->parse(['history' => $this->history]);
        $this->s::enqueueNotice($notice_markup, [
            'type'         => 'success',
            'is_transient' => !$this->history['first_time'],
        ]);
        if ($this->App->Config->§specs['§is_pro'] && !$this->s::getOption('§license_key')) {
            $license_key_Template      = $this->c::getTemplate('s-core/notices/license-key.php');
            $license_key_notice_markup = $license_key_Template->parse();
            $this->s::enqueueNotice($license_key_notice_markup, [
                'id'             => '§license-key',
                'type'           => 'info',
                'is_persistent'  => true,
                'is_dismissable' => false,
            ]);
        }
    }

    /**
     * Update installed version.
     *
     * @since 160524 Install utils.
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

        $this->s::sysOption('install_history', $this->history);
    }
}
