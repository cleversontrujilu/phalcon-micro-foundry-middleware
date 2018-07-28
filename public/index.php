<?php

use Phalcon\Di\FactoryDefault;

ini_set("display_errors" , 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {

    /**
     * The FactoryDefault Dependency Injector automatically registers the services that
     * provide a full stack framework. These default services can be overidden with custom ones.
     */
    $DI = new FactoryDefault();

    /**
     * Include constants
     */
    include APP_PATH . '/config/constants.php';

    /**
     * Include Services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $DI->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

	if(is_file(BASE_PATH . "/vendor/autoload.php"))
		include BASE_PATH . "/vendor/autoload.php";

    /**
     * Include Application
     */
    include APP_PATH . '/app.php';


} catch (\Exception $e) {
      echo $e->getMessage() . '<br>';
      echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
