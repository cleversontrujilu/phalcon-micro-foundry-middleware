<?php
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

use Phalcon\Mvc\Micro\Collection as MicroCollection;


$content = new MicroCollection();

// Define a classe controller manipuladora da requisição e define o parametro de LazyLoading
$content->setHandler('ContentController' , true);
// Prefixo das chamadas
$content->setPrefix('/content');

//Define a rota /
$content->get( '/'   , 'index');
$content->post('/'   , 'add');

// Define a chamada
$app->mount($content);

/**
 * Rotas a partir de função
 */
$app->get('/', function () use($app) {
    $app->forge->setData("Debes golpear el hierro cuando aun esta al rojo vivo - Publio Siro!");    
});

/**
 * Not found handler
 */
$app->notFound(function () use($app) {

    $app->response->setStatusCode(404, 'Not Found');
    $app->response->sendHeaders();
    $app->response->setHeader("Content-Type" , "application/json");

    $message = json_encode(
        [
            'status'  => 'not_found',
            'code'    => 404,
            'message' => "Endpoint inexistente",
        ]
    );

    $app->response->setContent($message);
    $app->response->send();

});

/**
 * Error handler
 */
$app->error(
    function ($exception) use ($app){

      $app->response->setStatusCode(400 , 'Bad Request');
      $app->response->sendHeaders();
      $app->response->setHeader("Content-Type" , "application/json");

        echo json_encode(
            [
                'status'  => 'error',
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
            ]
        );
        return false;
    }
);
