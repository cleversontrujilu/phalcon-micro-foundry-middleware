<?php
/**
 * Local variables
 * @var Phalcon\Di\FactoryDefault $DI
 */

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\Collection as MicroCollection;
use Phalcon\Events\Manager as EventsManager;

use Middleware\CacheMiddleware;
use Middleware\RequestMiddleware;
use Middleware\ResponseMiddleware;

$app   = new Micro($DI);
$event = new EventsManager();

/*
 * Before process request
 */
$event->attach('micro:beforeExecuteRoute', new CacheMiddleware()); // :beforeExecuteRoute
$app->before(new CacheMiddleware());

$event->attach('micro', new RequestMiddleware()); // :beforeExecuteRoute
$app->before(new RequestMiddleware());

/*
 * After Request
 */
$event->attach('micro', new ResponseMiddleware()); // :afterExecuteRoute
$app->after(new ResponseMiddleware());

/**
 * Make sure our events manager is in the DI container now
 */
$app->setEventsManager($event);

/*
 *  Default response system, the system it's OK!!
 */
$app->get('/', function () use ($app) {
    return "Debes golpear el hierro cuando aun esta al rojo vivo - Publio Siro!";
});

/**
 * Not found handler
 */
$app->notFound(function () use ($app) {
    $app->response->setStatusCode(404, 'Not Found');
    $app->response->sendHeaders();
    $app->response->setHeader("Content-Type", "application/json");

    $message = json_encode(
        [
            'status'  => 'not_found',
            'code'    => 404,
            'message' => "Recurso inexistente",
        ]
    );

    $app->response->setContent($message);
    $app->response->send();
});

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app) {
        // TODO: Diferença entre status 400 e  500
        $app->response->setStatusCode(500, 'Bad Request');
        $app->response->sendHeaders();
        $app->response->setHeader("Content-Type", "application/json");

        $message = json_encode(
              [
                  'status'  => 'error',
                  'code'    => $exception->getCode(),
                  'message' => $exception->getMessage(),
              ]
          );

        $app->response->setContent($message);
        $app->response->send();
        die();
    }
);

$app->handle();
