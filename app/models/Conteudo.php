<?php

class Conteudo extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=20, nullable=true)
     */
    protected $editoria_id;

    /**
     *
     * @var string
     * @Column(type="string", length=100, nullable=true)
     */
    protected $refid;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $titulo;

    /**
     *
     * @var string
     * @Column(type="string", length=2048, nullable=true)
     */
    protected $url;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $dt_conteudo;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $cont_comentario;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    protected $status;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=true)
     */
    protected $origem;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    protected $social_uuid;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $retranca;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field editoria_id
     *
     * @param integer $editoria_id
     * @return $this
     */
    public function setEditoriaId($editoria_id)
    {
        $this->editoria_id = $editoria_id;

        return $this;
    }

    /**
     * Method to set the value of field refid
     *
     * @param string $refid
     * @return $this
     */
    public function setRefid($refid)
    {
        $this->refid = $refid;

        return $this;
    }

    /**
     * Method to set the value of field titulo
     *
     * @param string $titulo
     * @return $this
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Method to set the value of field url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Method to set the value of field dt_conteudo
     *
     * @param string $dt_conteudo
     * @return $this
     */
    public function setDtConteudo($dt_conteudo)
    {
        $this->dt_conteudo = $dt_conteudo;

        return $this;
    }

    /**
     * Method to set the value of field cont_comentario
     *
     * @param integer $cont_comentario
     * @return $this
     */
    public function setContComentario($cont_comentario)
    {
        $this->cont_comentario = $cont_comentario;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Method to set the value of field origem
     *
     * @param integer $origem
     * @return $this
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;

        return $this;
    }

    /**
     * Method to set the value of field social_uuid
     *
     * @param string $social_uuid
     * @return $this
     */
    public function setSocialUuid($social_uuid)
    {
        $this->social_uuid = $social_uuid;

        return $this;
    }

    /**
     * Method to set the value of field retranca
     *
     * @param string $retranca
     * @return $this
     */
    public function setRetranca($retranca)
    {
        $this->retranca = $retranca;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field editoria_id
     *
     * @return integer
     */
    public function getEditoriaId()
    {
        return $this->editoria_id;
    }

    /**
     * Returns the value of field refid
     *
     * @return string
     */
    public function getRefid()
    {
        return $this->refid;
    }

    /**
     * Returns the value of field titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Returns the value of field url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the value of field dt_conteudo
     *
     * @return string
     */
    public function getDtConteudo()
    {
        return $this->dt_conteudo;
    }

    /**
     * Returns the value of field cont_comentario
     *
     * @return integer
     */
    public function getContComentario()
    {
        return $this->cont_comentario;
    }

    /**
     * Returns the value of field status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the value of field origem
     *
     * @return integer
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * Returns the value of field social_uuid
     *
     * @return string
     */
    public function getSocialUuid()
    {
        return $this->social_uuid;
    }

    /**
     * Returns the value of field retranca
     *
     * @return string
     */
    public function getRetranca()
    {
        return $this->retranca;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("social");
        $this->setSource("conteudo");
        $this->hasMany('id', 'Comentario', 'conteudo_id', ['alias' => 'Comentario']);
        $this->hasMany('id', 'Contato', 'conteudo_id', ['alias' => 'Contato']);
        $this->hasMany('id', 'ConteudoCompart', 'conteudo_id', ['alias' => 'ConteudoCompart']);
        $this->belongsTo('editoria_id', '\Editoria', 'id', ['alias' => 'Editoria']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Conteudo[]|Conteudo|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Conteudo|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'conteudo';
    }

}
