<?php
/**
 * Plugin.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

use WebSharks\WpSharks\Core\Classes\App;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
add_action('plugins_loaded', function () {
    require_once __DIR__.'/stub.php'; // Autoloader.
    new App(); // Instantiate WP Sharks Core plugin instance.
}, -10000); // Hook priority; i.e., before other plugins depending on this.
