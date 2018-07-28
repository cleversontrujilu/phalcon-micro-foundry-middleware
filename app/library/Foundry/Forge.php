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


    public function run(\Phalcon\Mvc\Micro $app)
    {
    	$this->App = $app;

        $this->setCachekeyName();

        $this->setHeaderCache();

        $this->restoreCache();

        $this->sendHeaderRequest();

        return $this->send();
    }

    /**
     * Instancia da Classe foundry/provision
     *
     * @var object
     */
    public function send()
    {
        if (!$this->Data) {
            return true;
        }

        $payload = [
            'status'   => 'success',
            'response' => $this->Data,
        ];

       $this->App->response->setJsonContent($payload);
       $this->App->response->send();

		return false;
    }

    public function finish()
    {
		$this->setData()
			 ->saveCache()
			 ->send();
    }

	private function setData()
	{
		$this->Data =$this->App->getReturnedValue();
		return $this;
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

    private function saveCache()
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

    private function setHeaderCache()
    {
        if (!$this->StoredCache) {
            $this->lastModified = date("D, d M Y H:i:s");
            $this->ETag         = md5($this->CacheActionName . $this->lastModified);
        }
        $this->cacheTime    = (!empty($this->Provision->getConfig("cacheTime")) or $this->Provision->getConfig("cacheTime") === 0)? $this->Provision->getConfig("cacheTime") : DEFAULT_CACHE_TIME;
    }

    private function sendHeaderRequest()
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
}
