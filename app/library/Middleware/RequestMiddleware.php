<?php
namespace Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Events\Event;

class RequestMiddleware implements MiddlewareInterface
{
    /**
     * Calls the middleware
     *
     * @param Micro $application
     *
     * @returns bool
     */
    public function call(Micro $application)
    {
        return true;
    }

    /**
     * Before anything happens
     *
     * @param Event $event
     * @param Micro $application
     *
     * @returns bool
     */
    public function beforeExecuteRoute(Event $event, Micro $app)
    {
        $provision    = $app->getDI()->get("provision");
        $provision->run($app);

        if ($provision->getStatus() !== true) {
            return false;
        }

        return true;
    }

    /**
     * Before anything happens 02
     *
     * @param Event $event
     * @param Micro $application
     *
     * @returns bool
     */
    public function beforeHandleRoute(Event $event, Micro $app)
    {
        $provision    = $app->getDI()->get("provision");
        $provision->init($app);
        return true;
    }
}
