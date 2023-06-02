<?php
set_include_path ('library/');
define('BASE_PATH', dirname(dirname(__FILE__)));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('LANG_PATH', BASE_PATH . '/data/lang');
set_include_path( get_include_path() .
                  PATH_SEPARATOR . APPLICATION_PATH . '/models' .
                  PATH_SEPARATOR . BASE_PATH . '/library' .
                  PATH_SEPARATOR . BASE_PATH
);
// APPLICATION_ENV defines which config section is loaded
if(!defined('APPLICATION_ENV')) {
    // define('APPLICATION_ENV', 'production');
    define('APPLICATION_ENV', 'development');
}
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV,
    APPLICATION_PATH.'/config/config.ini');

$application->bootstrap();

$application->run();
