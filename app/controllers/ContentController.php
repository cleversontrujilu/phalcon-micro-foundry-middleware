<?php
use Phalcon\Mvc\Controller;

class ContentController extends Controller
{
    public function index()
    {
        return ['payload' => $this->provision->getBody()];
    }

    public function create()
    {
        return ['payload' => $this->provision->getBody()];
    }

    public function update()
    {
        return ['payload' => $this->provision->getBody()];
    }

    public function listTags($id)
    {
        return ['payload' => $this->provision->getBody() , 'id' => $id];
    }
}
