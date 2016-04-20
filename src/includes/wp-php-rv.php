<?php
// PHP v5.2 compatible.

if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
$GLOBALS['wp_php_rv'] = array(
    'rv' => '7.0.4', //php-required-version//
    're' => array(
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
    ),
);
