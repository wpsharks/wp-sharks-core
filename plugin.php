<?php
/*
Version: 160229
Text Domain: wp-sharks-core
Plugin Name: WP Sharks Core

Author: WP Sharks™
Author URI: https://wpsharks.com/

Plugin URI: https://wpsharks.com/
Description: The WP Sharks Core is a WordPress plugin that serves as a framework for other plugins by WP Sharks™.
*/
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require __DIR__.'/src/includes/wp-php-rv.php';

if (require(__DIR__.'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once __DIR__.'/src/includes/plugin.php';
}
