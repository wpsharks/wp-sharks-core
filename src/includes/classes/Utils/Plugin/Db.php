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
 * Db utils.
 *
 * @since 16xxxx DB utils.
 */
class Db extends WCoreClasses\PluginBase
{
    /**
     * WP database.
     *
     * @since 16xxxx DB utils.
     *
     * @type \wpdb Reference.
     */
    public $wp;

    /**
     * Class constructor.
     *
     * @since 16xxxx DB utils.
     *
     * @param Plugin $Plugin Instance.
     */
    public function __construct(Plugin $Plugin)
    {
        parent::__construct($Plugin);

        $this->wp = &$GLOBALS['wpdb'];
    }

    /**
     * Table prefix.
     *
     * @since 16xxxx DB utils.
     *
     * @return string Table prefix.
     */
    public function prefix(): string
    {
        return $this->wp->prefix.$this->Plugin->Config->brand['base_prefix'].'_';
    }

    /**
     * Create missing DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function createMissingTables()
    {
        $tables_dir = $this->Plugin->Config->db['tables_dir'];
        $Tables     = c\dir_regex_recursive_iterator($tables_dir, '/\.sql$/ui');

        foreach ($Tables as $_Table) {
            if (!$_Table->isFile()) {
                continue; // Bypass.
            }
            $_sql_file = $_Table->getPathname();

            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $this->prefix().$_sql_file_table;

            $_sql = c\mb_trim(file_get_contents($_sql_file));
            $_sql = str_replace('%%prefix%%', $this->prefix(), $_sql);
            $_sql = $this->fulltextCompat($_sql);

            if (!preg_match('/^CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\b/ui', $_sql)) {
                $_sql = preg_replace('/^CREATE\s+TABLE\b/ui', 'CREATE TABLE IF NOT EXISTS', $_sql);
            }
            if (!$this->wp->query($_sql)) { // Table creation failure?
                throw new Exception(sprintf('DB table creation failure. Table: `%1$s`. SQL: `%2$s`.', $_sql_file_table, $_sql));
            }
        } // unset($_Table, $_sql_file, $_sql_file_table, $_sql);
    }

    /**
     * Drop existing DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function dropExistingTables()
    {
        $tables_dir = $this->Plugin->Config->db['tables_dir'];
        $Tables     = c\dir_regex_recursive_iterator($tables_dir, '/\.sql$/ui');

        foreach ($Tables as $_Table) {
            if (!$_Table->isFile()) {
                continue; // Bypass.
            }
            $_sql_file = $_Table->getPathname();

            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $this->prefix().$_sql_file_table;

            if (!$this->wp->query('DROP TABLE IF EXISTS `'.esc_sql($_sql_file_table).'`')) {
                throw new Exception(sprintf('DB table drop failure: `%1$s`.', $_sql_file_table));
            }
        } // unset($_Table, $_sql_file, $_sql_file_table);
    }

    /**
     * Fulltext index compat.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $sql SQL to check.
     *
     * @return string Output `$sql` w/ possible engine modification.
     *                Only MySQL v5.6.4+ supports fulltext indexes with the InnoDB engine.
     *                Otherwise, we use MyISAM for any table that includes a fulltext index.
     *
     * @note  MySQL v5.6.4+ supports fulltext indexes w/ InnoDB.
     *    See: <http://bit.ly/ZVeF42>
     */
    public function fulltextCompat(string $sql): string
    {
        $sql = c\mb_trim($sql); // For accurate regex matches.

        if (!preg_match('/^CREATE\s+TABLE\b/ui', $sql)) {
            return $sql; // Not applicable.
        }
        if (!preg_match('/\bFULLTEXT\s+KEY\b/ui', $sql)) {
            return $sql; // No fulltext index.
        }
        if (!preg_match('/\bENGINE\=InnoDB\b/ui', $sql)) {
            return $sql; // Not using InnoDB anyway.
        }
        if (version_compare($this->wp->db_version(), '5.6.4', '>=')) {
            return $sql; // v5.6.4+ supports fulltext in InnoDB.
        }
        return preg_replace('/\bENGINE\=InnoDB\b/ui', 'ENGINE=MyISAM', $sql);
    }
}
