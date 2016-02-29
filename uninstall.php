<?php
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require __DIR__.'/src/includes/wp-php-rv.php';

if (require(__DIR__.'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once __DIR__.'/src/includes/uninstall.php';
}
