<?php
namespace Foundry;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception;
use Phalcon\Http\Request;
use Phalcon\Filter;
use Phalcon\Validation;

/**
 * Provision Class - Biblioteca de validação e provisionamento para a requisição
 *
 * @category   Validation
 * @package    Webservice
 * @subpackage Foundry
 * @version    3
 */
class Provision
{

  /**
    * Lista de parametros enviados pela requisição
    *
    * @var array
    */
    private $Params;

    /**
      * Lista de erros gerados pelos paramentros enviados
      *
      * @var array
      */
    private $Errors;

    /**
     * Status geral da validação
     *
     * @var boolean
     */
    private $Status;

    /**
     * lista de Regras para validação da requisição
     *
     * @var array
     */
    private $Rules;

    /**
     * Intancia Dispatcher do Phalcon
     *
     * @var array
     */
    private $Dispatcher;

    /**
     * Arquivo de configuração da API
     *
     * @var array
     */
    private $Resource;


    /**
     * Arquivo de configuração da API
     *
     * @object \Phalcon\Mvc\Micro
     */
    private $App;


    public function __construct()
    {
        $this->Status   = true;
        $this->Errors   = [];
        $this->Params   = [];
        $this->Rules    = false;
        $this->Resource = require APP_PATH . "/config/api.php";
        $this->Url      = $this->setUrl();
        $this->Request  = new Request();
        $this->Configs  = [];

        $this->Dispatcher = \Phalcon\DI::getDefault()->getShared("dispatcher");
    }

    public function setHandler(\Phalcon\Mvc\Micro $app)
    {
        // Set app
        $this->App = $app;

        // Caso nenhuma configuração for definida
        $config  = $this->Resource->toArray();
        if (empty($config)) {
            return false;
        }

        // Caso não haja nenhuma configuração para aquele recurso
        if (!isset($config[$this->URL[0]])) {
            return false;
        }

        if (!isset($config[$this->URL[0]]['handler'])) {
            return false;
        }

        $content = new \Phalcon\Mvc\Micro\Collection();
        $resorce = $config[$this->URL[0]];

        $content->setPrefix($this->URL[0]);
        $content->setHandler($resorce['handler'], true);

        foreach ($resorce['patterns'] as $route => $array) {
            $action = explode("@", $route);
            $method = (isset($action[1]))? strtolower($action[1]) : "map";
            $content->$method($action[0], $array['action']);
        }
        $app->mount($content);

        // Prefixo das chamadas
        // Define a classe controller manipuladora da requisição e define o parametro de LazyLoading

        //Define a rota /
        //$content->get( '/'         , 'index');
        //$content->get( '/corvo'    , 'index');
        //$content->post('/'         , 'add');
        //$app->mount($content);

        return true;
    }

    public function run()
    {

        // Set a váriavel de pattern da requisiçãoptimize
        $this->setPattern();

        // Atribui a $Rules as regras de validação
        $this->setRules();

        // Atribui a $Configs as regras de configuração do endpoint
        $this->setConfigs();

        // Atribui os parametros da requisição
        $this->setParams();

        // Valida os parametros recebidos
        $this->checkParams();

        // Valida e filtra os parametros recebidos
        $this->filterParams();

        // Finaliza a camada de validação, retorna uma exception com os erros acumulados
        $this->finish();
    }


    /*********************************** Métodos de validação ***************************************/
    private function checkParams()
    {
        if (!$this->Rules) {
            return;
        }

        $fields = $this->Rules->get("fields");
        if (!$fields) {
            return false;
        }

        $validation = new Validation();

        foreach ($fields as $field) {
            $Rules = explode("|", $field['rules']);

            foreach ($Rules as $rule) {
                $ValidatorClass = "Phalcon\Validation\Validator\\" . $rule;
                $validation->add(
                      $field->get("field"),
                      new $ValidatorClass()
                );
            }
        }

        $messages = $validation->validate($this->Params);
        if (count($messages) != 0) {
            $this->Status = false;
            foreach ($messages as $message) {
                $this->Errors[] = $message->getMessage();
            }
        }
    }

    private function filterParams()
    {
        if (!$this->Rules) {
            return;
        }

        $fields = $this->Rules->get("fields");
        if (!$fields or $this->Status == false) {
            return;
        }

        $filter = \Phalcon\DI::getDefault()->getShared("filter");

        foreach ($fields as $field) {
            $filters                       = (isset($field['filters']))? explode("|", $field['filters']) : [];
            $filters                       = array_merge(["trim","striptags","trim"], $filters);
            $this->Params[$field['field']] = $filter->sanitize($this->Params[$field['field']], $filters);
        }
    }

    private function finish()
    {
        if ($this->Status == false) {
            throw new Exception(json_encode($this->Errors));
        } else {
            foreach ($this->Params as $key => $value) {
                $this->Dispatcher->setParams($this->Params);
            }
        }
    }

    /*********************************** Suporte ***************************************/
    private function needConfig($config = false)
    {
		if(empty($this->Configs)) {
			return false;
		}

        if ($this->Configs->get($config)) {
            return true;
        } else {
            return false;
        }
    }

    /*********************************** GETTERS ***************************************/
    public function getParams()
    {
        return $this->Dispatcher->getParams();
    }

    public function getDispatcher()
    {
        return $this->Dispatcher;
    }

    public function getStatus()
    {
        return $this->Status;
    }

    public function getRules()
    {
        return $this->Rules;
    }

    public function getParam($key = false, $filter = null)
    {
        return $this->Dispatcher->getParam($key);
    }

    public function getConfig($config = false)
    {
        if (empty($config) || empty($this->Configs)) {
            return false;
        }

        return $this->Rules->get("configs")->get($config);
    }

    public function getPattern()
    {
        return $this->Pattern;
    }

    /*********************************** SETTERS ***************************************/
    private function setParams()
    {
        $this->Params['control'] = date("Y-m-d");

        foreach ($this->Request->get() as $key => $value) {
            $this->Params[$key] = $value;
        }

        // os valores de $_POST sobscrevem sempre os Valores de $_GET
        foreach ($this->Request->getPost() as $key => $value) {
            $this->Params[$key] = $value;
        }
    }

    public function setParam($key = false, $value = false)
    {
        $this->Params[$key] = $value;
    }

    private function setRules()
    {
        $resource = $this->Resource->get($this->URL[0]);
        //s($resource->get("patterns")->get("/corvo/{id}/cabrito"));
		if(!$resource)
			return false;

		$method = $this->Request->getMethod();

        $this->Rules = $resource->get("patterns")->get($this->Pattern . "@" . $method);

        if (!$this->Rules) {
            $this->Rules = $resource->get("patterns")->get($this->Pattern);
        }

        if (!$this->Rules and ALLOW_UNDECLARED_REQUEST === false) {
            $this->Errors[] = "Nenhuma configuração encontrada para esse endpoint";
            $this->Status   = false;
        }
    }

    private function setConfigs()
    {
        if(!$this->Rules)
            return false;

        $this->Configs = $this->Rules->get("configs");
    }

    private function setPattern()
    {
        $this->Pattern       = str_replace($this->URL[0], '', $this->App->getRouter()->getMatchedRoute()->getPattern());
    }

    private function setUrl()
    {
        // quebra a URL
        $this->URL = explode("/", $_GET['_url']);

        // remove o primeiro item do array, sempre vazio;
        array_splice($this->URL, 0, 1);

        // Adiciona novamente a slice /
        foreach ($this->URL as $i => $value) {
            $this->URL[$i] = "/" . $value;
        }
    }
}
