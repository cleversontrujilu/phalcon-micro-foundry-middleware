<?php
namespace Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;


class ResponseMiddleware implements MiddlewareInterface
{
    /**
     * Calls the middleware
     *
     * @param Micro $application
     *
     * @returns bool
     */
     public function call(Micro $app)
     {
         $payload = [
             'status'   => 'success',
             'response' => $app->getReturnedValue(),
         ];

         $app->response->setJsonContent($payload);
         $app->response->send();

         return true;
     }
}
