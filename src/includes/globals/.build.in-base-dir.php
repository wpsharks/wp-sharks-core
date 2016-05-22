<?php
// @codingStandardsIgnoreFile

declare (strict_types = 1);
namespace WebSharks\WpSharks\Globals;

error_reporting(-1);
ini_set('display_errors', 'yes');

if (PHP_SAPI !== 'cli') {
    exit('Requires CLI access.');
}
$load = '<?php
declare (strict_types = 1);
namespace '.__NAMESPACE__.';

if (!defined(\'WPINC\')) {
    exit(\'Do NOT access this file directly: \'.basename(__FILE__));
}
';
foreach (dir_recursive_regex(__DIR__, '/\.php$/ui') as $_file) {
    if (mb_strpos(basename($_sub_path_name = $_file->getSubPathname()), '.') !== 0) {
        $load .= "require_once __DIR__.'/".$_file->getSubPathname()."';\n";
    }
} // unset($_file); // Housekeeping.

file_put_contents(__DIR__.'/.load.php', $load);
echo $load; // Print for debugging purposes.

function dir_recursive_regex(string $dir, string $regex): \RegexIterator
{
    $DirIterator      = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_SELF | \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS);
    $IteratorIterator = new \RecursiveIteratorIterator($DirIterator, \RecursiveIteratorIterator::CHILD_FIRST);
    $RegexIterator    = new \RegexIterator($IteratorIterator, $regex, \RegexIterator::MATCH, \RegexIterator::USE_KEY);

    return $RegexIterator;
}
