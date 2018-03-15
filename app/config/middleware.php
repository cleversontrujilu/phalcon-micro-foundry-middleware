<?php
use Phalcon\Mvc\Micro;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

/*
 * Valida a requisição e verifica se há um cache dela
 */
$eventsManager = new EventsManager();

$eventsManager->attach(
    'micro:beforeExecuteRoute',
    function (Event $event, $app) {
        $provision    = $app->getDI()->get("provision");
        $forge        = $app->getDI()->get("forge");

        $provision->run($app);

        return $forge->run();
    }
);

$app->setEventsManager($eventsManager);


/*
 * Envia a resposta setada e salva o cache caso deva salvar
 */
$eventsManager->attach(
    'micro:afterExecuteRoute',
    function (Event $event, $app) {
          $forge        = $app->getDI()->get("forge");
          $forge->finishProcess();
    }
);

$app->setEventsManager($eventsManager);
