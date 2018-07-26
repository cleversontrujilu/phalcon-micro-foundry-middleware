<?php
namespace Foundry;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception;
use Phalcon\Http\Request;
use Phalcon\Filter;
use Phalcon\Validation;

/**
 * Provision Class - Camada de validação e preparo da requisição
 *
 * @category   Validation
 * @package    Webservice
 * @subpackage Foundry
 * @version    2
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


    public function __construct()
    {
        $this->Status  = 1;
        $this->Errors  = [];
        $this->Params  = [];
        $this->Rules   = false;
        $this->Request = new Request();

        $this->Dispatcher = \Phalcon\DI::getDefault()->getShared("dispatcher");
    }

    public function run(\Phalcon\Mvc\Micro $App)
    {
        $this->setConfig($App);

        // Atribui a $Rules as regras de validação
        $this->setRules();

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
            $this->Status = 0;
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
        if (!$fields or $this->Status == 0) {
            return;
        }

        $filter = \Phalcon\DI::getDefault()->getShared("filter");

        foreach ($fields as $field) {
            $filters                       = (isset($field['filters']))? explode("|", $field['filters']) : [];
            $filters                       = array_merge(["trim","striptags","trim"], $filters);
            $this->Params[$field['field']] = $filter->sanitize($this->Params[$field['field']], $filters);
        }
    }

    /**
    * Verifica se o método precisa de um token social e o valida
    * TokenSocial é uma key criada para troca de informações com a sociabilização que necessita de memcached
    *
    * @see User::checkSocialToken()
    * @return boolean
    */
    private function checkSocialToken()
    {
    }

    /**
    * Verifica se o método precisa de um token de conteúdo e o valida
    *
    * @see User::checkToken()
    * @return boolean
    */
    private function checkContentToken()
    {
    }

    /**
      * Verifica se o metodo precisa de login do usuario e o valida
      *
      * @see User::checkToken()
      * @return boolean
      */
    private function checkLogin()
    {
    }

    private function finish()
    {
        if ($this->Status == 0) {
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
        if ($this->Rulez->get("config")->get($config)) {
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
        if (empty($config) || empty($this->Rules)) {
            return false;
        }

        return $this->Rules->get("configs")->get($config);
    }

    public function getPattern()
    {
        return $this->Pattern;
    }

    public function getMethod()
    {
        return $this->Method;
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
        $configs     = require APP_PATH . "/config/rulesApi.php";

        $this->RulesPattern = $configs->get($this->Pattern);
        if ($this->RulesPattern) {
            $this->Rules  = $this->RulesPattern->get($this->Method);
        }

        if (!$this->Rules and ALLOW_UNDECLARED_REQUEST === false) {
            $this->Errors[] = "Nenhuma configuração encontrada para esse método";
            $this->Status   = 0;
        }
    }

    private function setConfig($app)
    {
        $this->Pattern = $app->getRouter()->getMatchedRoute()->getPattern();
        $this->Method  = $this->Request->getMethod();
    }
}
