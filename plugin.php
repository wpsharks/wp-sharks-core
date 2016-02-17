<?php
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
$GLOBALS['wp_php_rv']['rv'] = '7.0.1'; //php-required-version//
$GLOBALS['wp_php_rv']['re'] = [
    'SPL',
    'Phar',
    'Reflection',

    'ctype',
    'date',
    'fileinfo',
    'filter',
    'gd',
    'hash',
    'json',
    'mbstring',
    'mcrypt',
    'posix',
    'session',
    'tokenizer',
    'zlib',

    'pcre',
    'openssl',
    'curl',
    'intl',
    'iconv',

    'dom',
    'xml',
    'libxml',
    'xmlreader',
    'xmlwriter',
    'SimpleXML',
];
if (require(dirname(__FILE__).'/src/vendor/websharks/wp-php-rv/src/includes/check.php')) {
    require_once dirname(__FILE__).'/src/includes/stub.php';
}
