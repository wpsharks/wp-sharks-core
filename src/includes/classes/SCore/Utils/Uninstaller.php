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
 * Uninstall utils.
 *
 * @since 16xxxx Install utils.
 */
class Uninstaller extends Classes\SCore\Base\Core
{
    /**
     * Counter.
     *
     * @since 16xxxx DB utils.
     *
     * @type int Counter.
     */
    protected $counter;

    /**
     * Class constructor.
     *
     * @since 16xxxx Uninstall utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->counter = 0; // Initialize.
    }

    /**
     * Install (or reinstall).
     *
     * @since 16xxxx Uninstall utils.
     */
    public function maybeUninstall()
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            return; // Not applicable.
        }
        if ($this->s::conflictsExist()) {
            return; // Stop on conflicts.
        }
        if (!$this->App->Config->§uninstall) {
            return; // Not uninstalling.
        }
        $this->counter = 0; // Initialize counter.

        if (is_multisite() && ($sites = wp_get_sites(['limit' => 10000]))) {
            foreach ($sites as $_site) {
                switch_to_blog($_site['blog_id']);
                $this->uninstall();
                restore_current_blog();
            } // unset($_site);
        } else {
            $this->uninstall();
        }
    }

    /**
     * Install (or reinstall).
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function uninstall()
    {
        $this->deleteOptions();
        $this->deleteTransients();
        $this->deletePostMeta();
        $this->deleteUserMeta();
        $this->dropDbTables();

        ++$this->counter;
    }

    /**
     * Delete option keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deleteOptions()
    {
        $WpDb = $this->s::wpDb();

        if ($this->counter <= 0 && is_multisite()) {
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
     * Delete option keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deleteTransients()
    {
        $WpDb = $this->s::wpDb();

        if ($this->counter <= 0 && is_multisite()) {
            $sql = /* Delete network transients. */ '
                    DELETE
                        FROM `'.esc_sql($WpDb->sitemeta).'`
                    WHERE
                        `meta_key` LIKE %s
                        OR `meta_key` LIKE %s
                ';
            $like1 = '%'.$WpDb->esc_like('_site_transient_'.$this->App->Config->©brand['©var'].'_').'%';
            $like2 = '%'.$WpDb->esc_like('_site_transient_timeout_'.$this->App->Config->©brand['©var'].'_').'%';

            $WpDb->query($WpDb->prepare($sql, $like1, $like2));
        }
        $sql = /* Delete transients. */ '
                DELETE
                    FROM `'.esc_sql($WpDb->options).'`
                WHERE
                    `option_name` LIKE %s
                    OR `option_name` LIKE %s
            ';
        $like1 = '%'.$WpDb->esc_like('_transient_'.$this->App->Config->©brand['©var'].'_').'%';
        $like2 = '%'.$WpDb->esc_like('_transient_timeout_'.$this->App->Config->©brand['©var'].'_').'%';

        $WpDb->query($WpDb->prepare($sql, $like1, $like2));
    }

    /**
     * Delete post meta keys.
     *
     * @since 16xxxx Uninstall utils.
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
     * @since 16xxxx Uninstall utils.
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
     * @since 16xxxx Uninstall utils.
     */
    protected function dropDbTables()
    {
        if (!$this->App->Config->§specs['§is_network_wide'] || $this->counter <= 0) {
            $this->s::dropDbTables(); // Only if the table prefix changes.
        }
    }
}
