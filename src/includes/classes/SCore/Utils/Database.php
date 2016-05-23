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
 * Database utils.
 *
 * @since 16xxxx DB utils.
 */
class Database extends Classes\SCore\Base\Core
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
     * Create DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function createTables()
    {
        $table_prefix = $this->prefix(); // For the app.
        $tables_dir   = $this->App->Config->§database['§tables_dir'];

        $indexes_dir  = $this->App->Config->§database['§indexes_dir'];
        $triggers_dir = $this->App->Config->§database['§triggers_dir'];

        if (!$tables_dir || !is_dir($tables_dir)) {
            return; // Not possible; no tables.
        }
        foreach ($this->c::dirRegexRecursiveIterator($tables_dir, '/\.sql$/ui') as $_Resource) {
            if (!$_Resource->isFile()) { // Not a file?
                continue; // Bypass; files only.
            }
            $_sql_file       = $_Resource->getPathname();
            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $table_prefix.$_sql_file_table;

            $_sql       = $this->c::mbTrim(file_get_contents($_sql_file));
            $_sql       = str_replace('%%table%%', $_sql_file_table, $_sql);
            $_sql       = $this->charsetCompat($this->engineCompat($this->ifNotExists($_sql)));
            $_sql_check = $this->wp->prepare('SHOW TABLES LIKE %s', $_sql_file_table);

            if ($this->wp->get_var($_sql_check) === $_sql_file_table) {
                continue; // Table exists already. Nothing to do.
            }
            if (!$_sql || $this->wp->query($_sql) === false) { // Table creation failure?
                throw $this->c::issue(sprintf('DB table creation failure. Table: `%1$s`. SQL: `%2$s`.', $_sql_file_table, $_sql));
            }
            foreach ([$indexes_dir, $triggers_dir] as $_tables_after_dir) {
                if ($_tables_after_dir && is_dir($_tables_after_dir)) {
                    // ↓ Looking for additional files for this specific table.
                    $_tables_after_regex = '/\/'.c::escRegex(basename($_sql_file)).'$/ui';

                    foreach ($this->c::dirRegexRecursiveIterator($_tables_after_dir, $_tables_after_regex) as $__Resource) {
                        if (!$__Resource->isFile()) { // Not a file?
                            continue; // Bypass; files only.
                        }
                        $__sql_file = $__Resource->getPathname();
                        $__sql      = $this->c::mbTrim(file_get_contents($__sql_file));
                        $__sql      = str_replace('%%table%%', $_sql_file_table, $__sql);

                        if ($this->wp->query($__sql) === false) { // Index creation failure?
                            throw $this->c::issue(sprintf('DB query failure. Table: `%1$s`. SQL: `%2$s`.', $_sql_file_table, $__sql));
                        }
                    } // unset($__Resource, $__sql_file, $__sql);
                }
            } // unset($_tables_after_dir, $_tables_after_regex);
        } // unset($_Resource, $_sql_file, $_sql_file_table, $_sql_check, $_sql);
    }

    /**
     * Drop DB tables.
     *
     * @since 16xxxx DB utils.
     */
    public function dropTables()
    {
        $table_prefix = $this->prefix(); // For the app.
        $tables_dir   = $this->App->Config->§database['§tables_dir'];

        if (!$tables_dir || !is_dir($tables_dir)) {
            return; // Not possible; no tables.
        }
        foreach ($this->c::dirRegexRecursiveIterator($tables_dir, '/\.sql$/ui') as $_Resource) {
            if (!$_Resource->isFile()) { // Not a file?
                continue; // Bypass; files only.
            }
            $_sql_file = $_Resource->getPathname();

            $_sql_file_table = basename($_sql_file, '.sql');
            $_sql_file_table = str_replace('-', '_', $_sql_file_table);
            $_sql_file_table = $table_prefix.$_sql_file_table;

            if ($this->wp->query('DROP TABLE IF EXISTS `'.esc_sql($_sql_file_table).'`') === false) {
                throw $this->c::issue(sprintf('DB table drop failure: `%1$s`.', $_sql_file_table));
            }
        } // unset($_Resource, $_sql_file, $_sql_file_table);
    }

    /**
     * MySQL `IF NOT EXISTS` check.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $sql SQL to check.
     *
     * @return string Output `$sql` with `IF NOT EXISTS`.
     */
    public function ifNotExists(string $sql): string
    {
        $sql = $this->c::mbTrim($sql);

        if (!preg_match('/^CREATE\s+TABLE\b/ui', $sql)) {
            return $sql; // Not applicable.
        }
        if (!preg_match('/^CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\b/ui', $sql)) {
            $sql = preg_replace('/^CREATE\s+TABLE\b/ui', 'CREATE TABLE IF NOT EXISTS', $sql);
        }
        return $sql;
    }

    /**
     * MySQL storage engine compat.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $sql SQL to check.
     *
     * @return string Output `$sql` with engine modification.
     */
    public function engineCompat(string $sql): string
    {
        $sql = $this->c::mbTrim($sql);

        if (!preg_match('/^CREATE\s+TABLE\b/ui', $sql)) {
            return $sql; // Not applicable.
        }
        $sql = preg_replace('/\bENGINE\=[%a-z0-9_\-]*/ui', '', $sql);

        if (!preg_match('/\bFULLTEXT\s+KEY\b/ui', $sql) || version_compare($this->wp->db_version(), '5.6.4', '>=')) {
            // MySQL v5.6.4+ supports fulltext indexes w/ InnoDB. See: <http://bit.ly/ZVeF42>
            $sql = preg_replace('/;$/u', ' ENGINE=InnoDB;', $sql);
        } else {
            $sql = preg_replace('/;$/u', ' ENGINE=MyISAM;', $sql);
        }
        return $sql;
    }

    /**
     * MySQL charset/collate compat.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $sql SQL to check.
     *
     * @return string Output `$sql` with charset/collate modification.
     */
    public function charsetCompat(string $sql): string
    {
        $sql = $this->c::mbTrim($sql);

        if (!preg_match('/^CREATE\s+TABLE\b/ui', $sql)) {
            return $sql; // Not applicable.
        }
        $sql = preg_replace('/\bDEFAULT\s+CHARSET\=[%a-z0-9_\-]*/ui', '', $sql);
        $sql = preg_replace('/\bCOLLATE\=[%a-z0-9_\-]*/ui', '', $sql);

        if (!empty($this->wp->charset) && !empty($this->wp->collate)) {
            $sql = preg_replace('/;$/u', ' DEFAULT CHARSET='.$this->wp->charset.' COLLATE='.$this->wp->collate.';', $sql);
        }
        return $sql;
    }
}
