<?php
namespace Foundry;

use Phalcon\Http\Response;
use Phalcon\Events\Event;

class Forge
{
    /**
     * Objeto final que será retornado para a requisição
     *
     * @var object
     */
    private $Response;

    /**
     * Resultado interno retornado dos métdos,
     *
     * @var mixed
     */
    private $Data;

    /**
     * Valor da Key para salvar o cache da requisição
     *
     * @var string
     */
    private $CacheActionName;

    /**
     * Valor salvo do Cache
     *
     * @var mixed
     */
    private $StoredCache;

    /**
     * Instancia da Classe foundry/provision
     *
     * @var object
     */
    private $Provision;

    public function __construct()
    {
        $this->Provision    = \Phalcon\DI::getDefault()->getShared("provision");
        $this->Cache        = \Phalcon\DI::getDefault()->getShared("cacheAdapter");
        $this->View         = \Phalcon\DI::getDefault()->getShared("view");
        $this->Header       = new Response();
    }

    /**
     * Instancia da Classe foundry/provision
     *
     * @var object
     */
    public function run()
    {
        return true;
    }


}
