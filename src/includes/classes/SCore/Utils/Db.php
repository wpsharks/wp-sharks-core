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
 * Db utils.
 *
 * @since 16xxxx DB utils.
 */
class Db extends Classes\SCore\Base\Core
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
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

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
        if ($this->App->Config->§specs['§is_network_wide']) {
            return $this->wp->base_prefix.$this->App->Config->©brand['©var'].'_';
        } else {
            return $this->wp->prefix.$this->App->Config->©brand['©var'].'_';
        }
    }

    /**
     * Create missing DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function createTables()
    {
        $table_prefix = $this->prefix();

        if (!is_dir($tables_dir = $this->App->Config->§db['§tables_dir'])) {
            return; // Nothing to do; i.e., no tables.
        }
        $Tables = $this->c::dirRegexRecursiveIterator($tables_dir, '/\.sql$/ui');

        foreach ($Tables as $_Table) {
            if (!$_Table->isFile()) {
                continue; // Bypass.
            }
            $_sql_file = $_Table->getPathname();

            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $table_prefix.$_sql_file_table;

            $_sql = $this->c::mbTrim(file_get_contents($_sql_file));
            $_sql = str_replace('%%prefix%%', $table_prefix, $_sql);
            $_sql = $this->fulltextCompat($_sql);

            if (!preg_match('/^CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\b/ui', $_sql)) {
                $_sql = preg_replace('/^CREATE\s+TABLE\b/ui', 'CREATE TABLE IF NOT EXISTS', $_sql);
            }
            if ($this->wp->query($_sql) === false) { // Table creation failure?
                throw new Exception(sprintf('DB table creation failure. Table: `%1$s`. SQL: `%2$s`.', $_sql_file_table, $_sql));
            }
        } // unset($_Table, $_sql_file, $_sql_file_table, $_sql);
    }

    /**
     * Drop existing DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function dropTables()
    {
        $table_prefix = $this->prefix();

        if (!is_dir($tables_dir = $this->App->Config->§db['§tables_dir'])) {
            return; // Nothing to do; i.e., no tables.
        }
        $Tables = $this->c::dirRegexRecursiveIterator($tables_dir, '/\.sql$/ui');

        foreach ($Tables as $_Table) {
            if (!$_Table->isFile()) {
                continue; // Bypass.
            }
            $_sql_file = $_Table->getPathname();

            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $table_prefix.$_sql_file_table;

            if ($this->wp->query('DROP TABLE IF EXISTS `'.esc_sql($_sql_file_table).'`') === false) {
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
        $sql = $this->c::mbTrim($sql); // For accurate regex matches.

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
