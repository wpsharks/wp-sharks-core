<?php
if (!defined('WPINC')) {
    exit('Do NOT access this file directly: '.basename(__FILE__));
}
$GLOBALS['wp_php_rv'] = [
    'rv' => '7.0.1', //php-required-version//
    're' => [
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
    ],
];
