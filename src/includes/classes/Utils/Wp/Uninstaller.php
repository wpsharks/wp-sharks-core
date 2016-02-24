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
 * Uninstall utils.
 *
 * @since 16xxxx Install utils.
 */
class Uninstaller extends WCoreClasses\PluginBase
{
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
        if ($this->Plugin->Utils->Conflicts->exist()) {
            return; // Stop on conflicts!
        }
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
        $this->deleteOptionKeys();
        $this->deleteTransientKeys();
        $this->deletePostMetaKeys();
        $this->deleteUserMetaKeys();
        $this->dropDbTables();
    }

    /**
     * Delete option keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deleteOptionKeys()
    {
        $Config = $this->Plugin->Config;
        $Db     = $this->Plugin->Utils->Db;

        $sql = /* Delete options. */ '
                DELETE
                    FROM `'.esc_sql($Db->wp->options).'`
                WHERE
                    `option_name` LIKE %s
                    OR `option_name` LIKE %s
            ';
        $like1 = $Db->wp->esc_like($Config->brand['base_var'].'_').'%';
        $like2 = '%'.$Db->wp->esc_like('_'.$Config->brand['base_var'].'_').'%';

        $Db->wp->query($Db->wp->prepare($sql, $like1, $like2));
    }

    /**
     * Delete option keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deleteTransientKeys()
    {
        $Config = $this->Plugin->Config;
        $Db     = $this->Plugin->Utils->Db;

        $sql = /* Delete transients. */ '
                DELETE
                    FROM `'.esc_sql($Db->wp->options).'`
                WHERE
                    `option_name` LIKE %s
                    OR `option_name` LIKE %s
            ';
        $like1 = '%'.$Db->wp->esc_like('_transient_'.$Config->brand['base_prefix'].'_').'%';
        $like2 = '%'.$Db->wp->esc_like('_transient_timeout_'.$Config->brand['base_prefix'].'_').'%';

        $Db->wp->query($Db->wp->prepare($sql, $like1, $like2));
    }

    /**
     * Delete post meta keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deletePostMetaKeys()
    {
        $Config = $this->Plugin->Config;
        $Db     = $this->Plugin->Utils->Db;

        $sql = /* Delete options. */ '
                DELETE
                    FROM `'.esc_sql($Db->wp->postmeta).'`
                WHERE
                    `meta_key` LIKE %s
                    OR `meta_key` LIKE %s
            ';
        $like1 = $Db->wp->esc_like($Config->brand['base_var'].'_').'%';
        $like2 = '%'.$Db->wp->esc_like('_'.$Config->brand['base_var'].'_').'%';

        $Db->wp->query($Db->wp->prepare($sql, $like1, $like2));
    }

    /**
     * Delete user meta keys.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function deleteUserMetaKeys()
    {
        $Config = $this->Plugin->Config;
        $Db     = $this->Plugin->Utils->Db;

        $sql = /* Delete options. */ '
                DELETE
                    FROM `'.esc_sql($Db->wp->usermeta).'`
                WHERE
                    `meta_key` LIKE %s
                    OR `meta_key` LIKE %s
            ';
        // The `wp_usermeta` table is global in scope.
        // i.e., This will actually run against ALL sites.
        $like1 = $Db->wp->esc_like($Config->brand['base_var'].'_').'%';
        $like2 = '%'.$Db->wp->esc_like('_'.$Config->brand['base_var'].'_').'%';

        $Db->wp->query($Db->wp->prepare($sql, $like1, $like2));
    }

    /**
     * Drop DB tables.
     *
     * @since 16xxxx Uninstall utils.
     */
    protected function dropDbTables()
    {
        $this->Plugin->Utils->Db->dropExistingTables();
    }
}
