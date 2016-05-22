<?php
// @codingStandardsIgnoreFile

declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
if (!defined('WP_VERSION')) {
    define('WP_VERSION', $GLOBALS['wp_version']);
}
