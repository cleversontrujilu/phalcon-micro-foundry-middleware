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
     * Status geral da validação
     *
     * @var boolean
     */
    private $Status;

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
     * Arquivo de configuração da API
     *
     * @var string
     */
    private $Resource;


    /**
     * Routa a partir do prefixo
     *
     * @var string
     */
    private $Route;


    public function __construct()
    {
        $this->Status        = true;
        $this->Errors        = [];
        $this->Params        = [];
        $this->ResorceConfig = [];
        $this->Rules         = null;
        $this->Route         = null;
        $this->Pattern       = null;

        $this->request       = new Request();
    }

    public function init(\Phalcon\Mvc\Micro $app)
    {
        $this->app = $app;

        $this->setUrl();

        $this->setResourceConfig();

        return $this->registerHandlerRoute();
    }

    public function registerHandlerRoute()
    {
        if (empty($this->ResorceConfig)) {
            return false;
        }

        $ResorceConfig = $this->ResorceConfig->toArray();

        if (!isset($ResorceConfig["handler"])) {
            return false;
        }

        $content = new \Phalcon\Mvc\Micro\Collection();
        $content->setPrefix($this->URL[0]);
        $content->setHandler($ResorceConfig['handler'], true);

        foreach ($ResorceConfig['routes'] as $route => $array) {
            $route = explode("@", $route);

            // TODO: validar os verbos existentes
            // Extrai o método da rota cadastrada na config
            $method = (isset($route[1]))? strtolower($route[1]) : "map";

            // Cadatra a route
            $content->$method($route[0], $array['action']);
        }

        $this->app->mount($content);

        return true;
    }

    public function run()
    {
        // Seta o nome do pattern
        $this->setPattern();

        // Seta o nome da rota apartir do prefix
        $this->setRoute();

        // Atribui as regras de validação da rota
        $this->setRulesValidation();

        // Atribui as configurações extras da rota
        $this->setRouteConfigs();

        // Atribui os parametros da requisição
        $this->setParams();

        // Valida os parametros recebidos
        $this->validateParams();

        // Valida e filtra os parametros recebidos
        $this->filterParams();

        // Finaliza
        $this->finish();
    }

    private function finish()
    {
        if ($this->Status == false) {
            throw new Exception(json_encode($this->Errors));
        }
    }

    /*********************************** Métodos de validação ***************************************/

    private function validateParams()
    {
        if (!$this->Rules) {
            return false;
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

    /*********************************** Métodos de Getters ***************************************/
    public function getStatus()
    {
        return $this->Status;
    }

    public function getRoute()
    {
        return $this->Route;
    }

    public function getPattern()
    {
        return $this->Pattern;
    }

    public function getConfig($config = false)
    {
        if (empty($config) || empty($this->Configs)) {
            return false;
        }

        return $this->Rules->get("configs")->get($config);
    }

    public function getBody()
    {
        return (object) $this->Params;
    }

    public function getParams()
    {
        return $this->Params;
    }

    public function getParam($param = false)
    {
        if (!$param || !isset($this->Params[$param])) {
            return false;
        }

        return $this->Params;
    }


    /*********************************** Private setters ***************************************/
    private function setParams()
    {
        $this->Params['control'] = date("Y-m-d H:i:s").microtime();

        foreach ($this->request->get() as $key => $value) {
            $this->Params[$key] = $value;
        }

        $raw_params = $this->request->getJsonRawBody();
        foreach ($raw_params as $key => $value) {
            $this->Params[$key] = $value;
        }
    }

    private function setPattern()
    {
        $this->Pattern = $this->app->getRouter()->getMatchedRoute()->getPattern();
    }

    private function setRoute()
    {
        $this->Route       = str_replace($this->URL[0], '', $this->Pattern);
        if (!$this->Route) {
            $this->Route = "/";
        }
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

    private function setRulesValidation()
    {
        if (!$this->ResorceConfig) {
            return false;
        }

        $this->Rules = $this->ResorceConfig->get("routes")->get($this->Route . "@" . $this->request->getMethod());

        //var_dump($this->ResorceConfig);
        //var_dump($this->Route . "@" . $this->request->getMethod());

        if (!$this->Rules) {
            $this->Rules = $this->ResorceConfig->get("routes")->get($this->Route);
        }

        if (!$this->Rules and ALLOW_UNDECLARED_REQUEST === false) {
            $this->Errors[] = "Nenhuma configuração encontrada para esse endpoint";
            $this->Status   = false;
        }
    }

    private function setRouteConfigs()
    {
        if (!$this->Rules) {
            return false;
        }

        $this->Configs = $this->Rules->get("configs");
    }

    private function setResourceConfig()
    {
        $resorces = require APP_PATH . "/config/resources.php";

        if (empty($resorces)) {
            return false;
        }

        if ($resorces->get($this->URL[0])) {
            $this->ResorceConfig = $resorces->get($this->URL[0]);
        }
    }
}
