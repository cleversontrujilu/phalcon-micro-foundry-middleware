<?php
/**
 * Local variables
 * @var Phalcon\Di\FactoryDefault $DI
 */

use Phalcon\Mvc\View\Simple as View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

/**
 * Shared configuration service
 */
$DI->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/*
 * Responsável por provisionar e testar os dados da requisição
 */
$DI->setShared("provision", function () {
    return new Foundry\Provision();
});


/*
 * Instancia a classe Forge, responsavel por tratar e o retorno da Requisição
 */
$DI->setShared("forge", function () {
    return new Foundry\Forge();
});

/**
 * Instancia a classe de filters e adiciona filtros personalisados
 */
$DI->setShared("filter", function () {
    return Support\Filters::initialize()->getFilter();
});

/*
 * Instancia a classe suporte para tratamento de cache
 */
$DI->setShared("cacheAdapter", function () {
    return new Support\CacheAdapter();
});


/**
 * Events Manager
 */
$DI->setShared("eventsManager", function () {
    return new EventsManager();
});

/**
 * Sets the view component
 */
$DI->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    return $view;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$DI->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$DI->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});
