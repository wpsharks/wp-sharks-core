<?php
/*
Version: 160229
Text Domain: wp-sharks-core
Plugin Name: WP Sharks Core

Author: WP Sharks™
Author URI: https://wpsharks.com/

License: GPL-3.0+
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Plugin URI: https://wpsharks.com/product/core/
Description: The WP Sharks Core is a WordPress plugin that serves as a framework for other plugins by the WP Sharks™ team.
*/
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
require __DIR__.'/src/includes/wp-php-rv.php';

if (require(__DIR__.'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once __DIR__.'/src/includes/plugin.php';
}