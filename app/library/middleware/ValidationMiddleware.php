<?php
namespace Middleware;

use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;


class ValidationMiddleware implements MiddlewareInterface
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
        var_dump($application);
        return false;
    }
}
