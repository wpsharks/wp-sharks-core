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
 * Debug utilities.
 *
 * @since 16xxxx URL to post ID.
 */
class Debug extends Classes\SCore\Base\Core implements CoreInterfaces\ByteConstants
{
    /**
     * Logs directory.
     *
     * @since 16xxxx Debug utilities.
     *
     * @type string Logs directory.
     */
    protected $logs_dir;

    /**
     * Max log file size.
     *
     * @since 16xxxx Debug utilities.
     *
     * @type int Max log file size.
     */
    protected $max_log_file_size;

    /**
     * Max log file age.
     *
     * @since 16xxxx Debug utilities.
     *
     * @type string Max log file age.
     */
    protected $max_log_file_age;

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

        $this->logs_dir          = $this->App->Config->©fs_paths['©logs_dir'].'/debug';
        $this->max_log_file_size = $this::BYTES_IN_MB * 2;
        $this->max_log_file_age  = strtotime('-7 days');
    }

    /**
     * Debug logging utility.
     *
     * @since 16xxxx Debug utilities.
     *
     * @param string $event Event name (e.g., `__METHOD__`).
     * @param mixed  $data  Data to log (e.g., `get_defined_vars()`).
     * @param string $note  Optional notes/description.
     */
    public function __invoke(string $event, $data = [], string $note = '')
    {
        if (!$this->App->Config->©debug['©log']) {
            return; // Not applicable.
        }
        $event = $this->cleanLogEvent($event); // Strip namespace, etc.

        $lines[] = __('Microtime:', 'wp-sharks-core').'    '.number_format(microtime(true), 8, '.', '');
        $lines[] = __('Event:', 'wp-sharks-core').'        '.($event ? $event : __('unknown event name', 'wp-sharks-core'));
        $lines[] = __('Note:', 'wp-sharks-core').'         '.($note ? $note : __('no note given by caller', 'wp-sharks-core'))."\n";

        $lines[] = __('System:', 'wp-sharks-core').'       '.PHP_OS.'; PHP v'.PHP_VERSION.' ('.PHP_SAPI.')';
        $lines[] = __('Software:', 'wp-sharks-core').'     WP v'.WP_VERSION.// Always include the WordPress version.
                                         (defined('WC_VERSION') ? '; WC v'.WC_VERSION : '').// WooCommerce.
                                         ($this->App->parent ? '; '.$this->App->parent->Config->©brand['©acronym'].' v'.$this->App->parent::VERSION : '').
                                        '; '.$this->App->Config->©brand['©acronym'].' v'.$this->App::VERSION."\n";

        if (($user = wp_get_current_user()) && $user->exists()) {
            $lines[] = __('User:', 'wp-sharks-core').'         #'.$user->ID.' @'.$user->user_login.' \''.$user->display_name.'\'';
        }
        $lines[] = __('User IP:', 'wp-sharks-core').'      '.($this->c::isCli() ? __('n/a; CLI process', 'wp-sharks-core') : $this->c::currentIp());
        $lines[] = __('User Agent:', 'wp-sharks-core').'   '.($this->c::isCli() ? __('n/a; CLI process', 'wp-sharks-core') : ($_SERVER['HTTP_USER_AGENT'] ?? ''))."\n";

        $lines[] = __('URL:', 'wp-sharks-core').'          '.($this->c::isCli() ? __('n/a; CLI process', 'wp-sharks-core') : $this->c::currentUrl())."\n";

        $lines[] = $this->c::mbTrim($this->c::dump($data, true), "\r\n"); // A dump of the data (variables).

        $this->writeLogFileLines($event, $lines); // Write the log entry.
    }

    /**
     * Write lines to log file.
     *
     * @since 16xxxx Debug utilities.
     *
     * @param string $event Event name.
     * @param array  $lines Log entry lines.
     *
     * @return int Total number of bytes written.
     */
    protected function writeLogFileLines(string $event, array $lines): int
    {
        if (!$lines) { // No lines?
            return; // Stop; nothing to do here.
        }
        $this->prepareLogsDir(); // Prepares (and secures) the logs directory.
        $file_name = mb_strpos($event, '#issue') !== false ? 'issues.log' : 'events.log';
        $file      = $this->logs_dir.'/'.$file_name;
        $this->maybeRotateLogFiles($file);

        $entry = implode("\n", $lines)."\n\n".str_repeat('-', 3)."\n\n";
        return (int) file_put_contents($file, $entry, LOCK_EX | FILE_APPEND);
    }

    /**
     * Cleans log event name.
     *
     * @since 16xxxx Debug utilities.
     *
     * @param string $event Log event name.
     *
     * @return string Cleaned log event name.
     */
    protected function cleanLogEvent(string $event): string
    {
        if (($classes_pos = mb_strripos($event, '\\Classes\\')) !== false) {
            $event = mb_substr($event, $classes_pos + 9);
        } // This chops off `*\Classes\` from `__METHOD__`.
        $event = str_replace($this->App->namespace, '', $event);

        return $event = $this->c::mbTrim($event, '', '\\');
    }

    /**
     * Prepares the logs directory.
     *
     * @since 16xxxx Debug utilities.
     */
    protected function prepareLogsDir()
    {
        if (is_dir($this->logs_dir)) {
            if (!is_writable($this->logs_dir)) {
                throw new Exception(sprintf('Logs directory not writable: `%1$s`.', $this->logs_dir));
            } // Always check to be sure the logs directory is still writable.
            return; // Otherwise; nothing to do here.
        }
        if (!mkdir($this->logs_dir, $this->App->Config->©fs_permissions['©transient_dirs'], true)) {
            throw new Exception(sprintf('Logs directory not writable: `%1$s`.', $this->logs_dir));
        } elseif (!$this->c::apacheHtaccessDeny($this->logs_dir)) {
            throw new Exception(sprintf('Unable to secure logs directory: `%1$s`.', $this->logs_dir));
        }
    }

    /**
     * Maybe rotate log files.
     *
     * @since 16xxxx Debug utilities.
     *
     * @param string $file Absolute file path.
     */
    protected function maybeRotateLogFiles(string $file)
    {
        if (!$file || !is_file($file)) {
            return; // Nothing to do at this time.
        } elseif (filesize($file) < $this->max_log_file_size) {
            return; // Nothing to do at this time.
        } // Only rotate when log file becomes large.

        rename($file, $this->uniqueSuffixLogFile($file));

        foreach ($this->c::dirRegexRecursiveIterator($this->logs_dir, '/\.log$/ui') as $_Resource) {
            if ($_Resource->isFile() && $_Resource->getMTime() < $this->max_log_file_age) {
                unlink($_Resource->getPathname());
            }
        } // unset($_Resource); // Housekeeping.
    }

    /**
     * Unique suffix log file.
     *
     * @since 16xxxx Debug utilities.
     *
     * @param string $file Absolute file path.
     *
     * @return string New unique (suffixed) log file.
     */
    protected function uniqueSuffixLogFile(string $file): string
    {
        return preg_replace('/\.log$/ui', '', $file).'-'.uniqid('', true).'.log';
    }
}
