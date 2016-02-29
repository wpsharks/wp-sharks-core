<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

use WebSharks\WpSharks\Core\Classes\App;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require_once __DIR__.'/stub.php';

new App(['§uninstall' => true]);
