<?php
/**
 * WP PHP RV.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
// PHP v5.2 compatible.

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
$GLOBALS['wp_php_rv'] = array(
    'os' => '', //os-required//

    'min'        => '7.0.4', //php-required-version//
    'bits'       => 64, //php-required-bits//
    'functions'  => array('eval'), //php-required-functions//
    'extensions' => array('SPL', 'Phar', 'Reflection', 'ctype', 'date', 'fileinfo', 'filter', 'gd', 'hash', 'json', 'mbstring', 'session', 'tokenizer', 'zlib', 'pcre', 'openssl', 'curl', 'intl', 'iconv', 'gmp', 'bcmath', 'dom', 'xml', 'libxml', 'xmlreader', 'xmlwriter', 'SimpleXML'), //php-required-extensions//

    'wp' => array(
        'min' => '4.7', //wp-required-version//
    ),
); // The following are back compat. keys.
$GLOBALS['wp_php_rv']['rv'] = $GLOBALS['wp_php_rv']['min'];
$GLOBALS['wp_php_rv']['re'] = $GLOBALS['wp_php_rv']['extensions'];
