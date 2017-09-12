<?php
/**
 * Uninstaller.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core;

use WebSharks\WpSharks\Core\Classes\App;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
require_once __DIR__.'/stub.php'; // Autoloader.
new App(['§uninstall' => true]); // Uninstall flag.
