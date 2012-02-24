<?php

defined('CONFIG')
    || define('CONFIG', dirname(__file__) . '/zprowl.ini');

defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'testing');

$includePath = array(
    '/media/data/www/zend/1.11/library',
    dirname(__file__) . '/../src/',
    get_include_path()
);

set_include_path(
    implode(PATH_SEPARATOR, $includePath)
);
