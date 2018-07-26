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
     *
     * @var string
     */
    private $CacheActionName;

    /**
     * Valor salvo do Cache
     *
     *
     * @var mixed
     */
    private $StoredCache;

    /**
     * Instancia da Classe foundry/provision
     *
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

    public function run()
    {
        // Set Cache key name
        $this->setCachekeyName();

        // Formato padrão da resposta
        $this->setDefaultResponse();

        // RestoreCache
        $this->restoreCache();

        // setHeaderCache
        $this->setHeaderCache();

        // headerRequest
        $this->headerRequest();

        // sendResponse
        return $this->sendCachedResponse();
    }


    private function setCachekeyName()
    {
        $args  = md5(http_build_query($this->Provision->getParams()));

        $this->CacheActionName = md5($this->Provision->getPattern() . $this->Provision->getMethod()) . "-"
                               . md5(http_build_query($this->Provision->getParams()));
    }

    private function restoreCache()
    {
        $this->StoredCache = $this->Cache->get($this->CacheActionName);
        if ($this->StoredCache) {
            $this->Data         = $this->StoredCache->Data;
            $this->lastModified = $this->StoredCache->lastModified;
            $this->ETag         = $this->StoredCache->ETag;
        }
    }

    private function setHeaderCache()
    {
        if (!$this->StoredCache) {
            $this->lastModified = date("D, d M Y H:i:s");
            $this->ETag         = md5($this->CacheActionName . $this->lastModified);
        }
        $this->cacheTime    = (!empty($this->Provision->getConfig("cacheTime")) or $this->Provision->getConfig("cacheTime") === 0)? $this->Provision->getConfig("cacheTime") : DEFAULT_CACHE_TIME;
    }


    private function HeaderRequest()
    {
        $this->Header->setHeader('Last-Modified', $this->lastModified ." GMT");
        $this->Header->setHeader('ETag', $this->ETag);
        $this->Header->setHeader('Cache-Control', 'max-age='.$this->cacheTime);

        $DoIDsMatch = (isset($_SERVER['HTTP_IF_NONE_MATCH']) and
        preg_match("/" . $this->ETag . "/", $_SERVER['HTTP_IF_NONE_MATCH']));

        if ($DoIDsMatch) {
            $this->Header->setRawHeader('HTTP/1.1 304 Not Modified');
            $this->Header->setRawHeader('Connection: close');
        }
    }

    private function setDefaultResponse()
    {
        $this->Response           = new \stdClass;
        $this->Response->status   = "success";
    }

    public function setData($data = false)
    {
        $this->Data = $data;
    }

    private function sendCachedResponse()
    {
        if (!$this->StoredCache) {
            return true;
        }

        $this->sendResponse();

        return false;
    }

    private function sendResponse()
    {
        if ($this->Data) {
            $this->Response->data     =  $this->Data;
        }

        $this->Header->setHeader("Content-Type", "application/json");
        $this->Header->setContent(json_encode($this->Response));
        $this->Header->send();
    }

    public function saveCacheAction()
    {
        if ($this->cacheTime !== 0) {
            $cache = new \stdClass();
            $cache->Data          = $this->Data;
            $cache->lastModified  = $this->lastModified;
            $cache->ETag          = $this->ETag;
            $this->Cache->save($this->CacheActionName, $cache, $this->cacheTime);
        }

        return $this;
    }

    public function finishProcess()
    {
        if (empty($this->Data))
            $this->setData();

        if ($this->cacheTime !== 0)
            $this->saveCacheAction();

        $this->sendResponse();
    }
}