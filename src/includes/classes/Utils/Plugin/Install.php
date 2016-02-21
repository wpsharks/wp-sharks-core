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
class Install extends WCoreClasses\PluginBase
{
    /**
     * Install time.
     *
     * @since 16xxxx Install utils.
     *
     * @type int Time.
     */
    public $time;

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

        $this->time = (int) get_option($this->Plugin->Config->brand['base_var'].'_install_time');
    }

    /**
     * Install (or reinstall).
     *
     * @since 16xxxx Install utils.
     */
    public function __invoke()
    {
        $this->maybeCreateDbTables();
        $this->maybeEnqueueNotice();
        $this->maybeSetInstallTime();
    }

    /**
     * Create DB tables.
     *
     * @since 16xxxx Install utils.
     */
    protected function maybeCreateDbTables()
    {
        $Db = $this->Plugin->Utils->Db;

        $Db->createTables();
    }

    /**
     * First time install displays notice.
     *
     * @since 16xxxx Install utils.
     */
    protected function maybeEnqueueNotice()
    {
        if ($this->time) {
            return; // Installed already.
        }
        if (!$this->Plugin->Config->notices['on_install']) {
            return; // Not configured to display a notice.
        }
        $Notices = $this->Plugin->Utils->Notices;

        $Notices->enqueue('', $this->Plugin->Config->notices['on_install']);
    }

    /**
     * Update installation time.
     *
     * @since 16xxxx Install utils.
     */
    protected function maybeSetInstallTime()
    {
        if ($this->time) {
            return; // Already set the time.
        }
        update_option($this->Plugin->Config->brand['base_var'].'_install_time', $this->time = time());
    }
}
