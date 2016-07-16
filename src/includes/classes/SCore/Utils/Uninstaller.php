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
 * Uninstall utils.
 *
 * @since 160524 Install utils.
 */
class Uninstaller extends Classes\SCore\Base\Core
{
    /**
     * Site counter.
     *
     * @since 160715 DB utils.
     *
     * @type int Site counter.
     */
    protected $site_counter;

    /**
     * Class constructor.
     *
     * @since 160524 Uninstall utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->site_counter = 0;
    }

    /**
     * Maybe uninstall.
     *
     * @since 160524 Uninstall utils.
     */
    public function maybeUninstall()
    {
        // See: <https://core.trac.wordpress.org/ticket/14955>
        if ($this->App->Config->§specs['§type'] !== 'plugin') {
            return; // For plugins only at this time.
        } elseif (!defined('WP_UNINSTALL_PLUGIN')) {
            return; // Not applicable.
        } elseif ($this->s::conflictsExist()) {
            return; // Stop on conflicts.
        } elseif (!$this->App->Config->§uninstall) {
            return; // Not uninstalling.
        }
        $this->site_counter = 0; // Initialize site counter.

        if ($this->Wp->is_multisite) { // For each site in the network.
            foreach (($sites = wp_get_sites()) ? $sites : [] as $_site) {
                ++$this->site_counter;
                switch_to_blog($_site['blog_id']);
                $this->uninstall();
                restore_current_blog();
            } // unset($_site);
        } else {
            ++$this->site_counter;
            $this->uninstall();
        }
    }

    /**
     * Install (or reinstall).
     *
     * @since 160524 Uninstall utils.
     */
    protected function uninstall()
    {
        // Uninstallers.
        $this->deleteOptions();
        $this->deletePostMeta();
        $this->deleteUserMeta();
        $this->dropDbTables();

        // Other uninstallers.
        $this->otherUninstallRoutines();
    }

    /**
     * Delete option keys.
     *
     * @since 160524 Uninstall utils.
     */
    protected function deleteOptions()
    {
        $WpDb = $this->s::wpDb();

        if ($this->Wp->is_multisite && $this->site_counter === 1) {
            $sql = /* Delete network options. */ '
                    DELETE
                        FROM `'.esc_sql($WpDb->sitemeta).'`
                    WHERE
                        `meta_key` LIKE %s
                        OR `meta_key` LIKE %s
                ';
            $like1 = $WpDb->esc_like($this->App->Config->©brand['©var'].'_').'%';
            $like2 = '%'.$WpDb->esc_like('_'.$this->App->Config->©brand['©var'].'_').'%';

            $WpDb->query($WpDb->prepare($sql, $like1, $like2));
        }
        $sql = /* Delete options. */ '
                DELETE
                    FROM `'.esc_sql($WpDb->options).'`
                WHERE
                    `option_name` LIKE %s
                    OR `option_name` LIKE %s
            ';
        $like1 = $WpDb->esc_like($this->App->Config->©brand['©var'].'_').'%';
        $like2 = '%'.$WpDb->esc_like('_'.$this->App->Config->©brand['©var'].'_').'%';

        $WpDb->query($WpDb->prepare($sql, $like1, $like2));
    }

    /**
     * Delete post meta keys.
     *
     * @since 160524 Uninstall utils.
     */
    protected function deletePostMeta()
    {
        $WpDb = $this->s::wpDb();

        $sql = /* Delete post meta. */ '
                DELETE
                    FROM `'.esc_sql($WpDb->postmeta).'`
                WHERE
                    `meta_key` LIKE %s
                    OR `meta_key` LIKE %s
            ';
        $like1 = $WpDb->esc_like($this->App->Config->©brand['©var'].'_').'%';
        $like2 = '%'.$WpDb->esc_like('_'.$this->App->Config->©brand['©var'].'_').'%';

        $WpDb->query($WpDb->prepare($sql, $like1, $like2));
    }

    /**
     * Delete user meta keys.
     *
     * @since 160524 Uninstall utils.
     */
    protected function deleteUserMeta()
    {
        $WpDb = $this->s::wpDb();

        $sql = /* Delete user meta. */ '
                DELETE
                    FROM `'.esc_sql($WpDb->usermeta).'`
                WHERE
                    `meta_key` LIKE %s
                    OR `meta_key` LIKE %s
            ';
        // The `wp_usermeta` table is global in scope.
        // i.e., This will actually run against ALL sites.
        $like1 = $WpDb->esc_like($this->App->Config->©brand['©var'].'_').'%';
        $like2 = '%'.$WpDb->esc_like('_'.$this->App->Config->©brand['©var'].'_').'%';

        $WpDb->query($WpDb->prepare($sql, $like1, $like2));
    }

    /**
     * Drop DB tables.
     *
     * @since 160524 Uninstall utils.
     */
    protected function dropDbTables()
    {
        // NOTE: This is optimized to avoid trying to delete tables over & over again.
        // If the app is network-wide and this is a network, there is only one set of tables.

        if ($this->App->Config->§specs['§is_network_wide'] && $this->Wp->is_multisite && $this->site_counter === 1) {
            $this->s::dropDbTables(); // Drop the network-wide tables for this app, just once.
            //
        } elseif (!$this->App->Config->§specs['§is_network_wide'] || !$this->Wp->is_multisite) {
            $this->s::dropDbTables(); // The table prefix changes for each site.
            // And, of course, this covers a standard WP installation also.
        }
    }

    /**
     * Other uninstall routines.
     *
     * @since 160524 Install utils.
     */
    protected function otherUninstallRoutines()
    {
        $this->s::doAction('other_uninstall_routines', $this->site_counter);
    }
}
