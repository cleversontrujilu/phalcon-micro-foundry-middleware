<?php
use Phalcon\Mvc\Controller;

class ContentController extends Controller
{
    public function index()
    {
        return ['abacate' => $this->provision->getBody()];
    }

    public function add()
    {
        return ['abacate' => $this->provision->getBody()];
    }
}
