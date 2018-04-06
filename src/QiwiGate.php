<?php

namespace QGA;

class QiwiGate
{
    protected $qiwiKey;
    protected $accountKey;

    public $error = false;
    public $response;
    public $link;

    protected $method;
    protected $options;
    protected $assoc;
    protected $type;
    protected $jsonResponse;

    /**
     * QiwiGate constructor
     * 
     * @param $purseAPIKey API ключ кошелька
     * @param $accountAPIKey API ключ аккаунта
     */
    public function __construct($purseAPIKey, $accountAPIKey)
    {
        $this->qiwiKey    = $purseAPIKey;
        $this->accountKey = $accountAPIKey;
    }

    /**
     * Установить API ключ кошелька
     *
     * @param $value API ключ кошелька
     */
    public function setPurseAPIKey($value)
    {
        $this->qiwiKey = $value;
    }

    /**
     * Отправить запрос
     *
     * @param $method имя метода
     * @param array $options параметры запроса
     * @param bool $assoc ассоциативный массив или объект
     * @return $this
     */
    public function sendRequest($method, $options = [], $assoc = true)
    {
        $this->setProperties($method, $options, $assoc);
        $this->initializeQuery();
        $this->checkResult();

        return $this;
    }

    protected function setProperties($method, $options, $assoc)
    {
        $this->method  = $method;
        $this->options = $options;
        $this->type    = explode('.', $method)[0];
        $this->assoc   = $assoc;
    }

    protected function initializeQuery()
    {
        $this->link         = $this->getLink();
        $this->jsonResponse = $this->parseJsonResponse();
        $this->response     = json_decode($this->jsonResponse, $this->assoc);
        
        if (empty($this->response)) {
            $this->error = true;
        }
    }
    
    protected function parseJsonResponse()
    {
        $response = file_get_contents($this->link);
        
        $parseResponse = str_replace('\\', ' ', $response);
        
        return $parseResponse;
    }

    protected function checkResult()
    {
        $result = json_decode($this->jsonResponse);

        if ($result->status === 'error') {
            $this->error = true;
        }
    }

    protected function getLink()
    {
        $link = 'https://qiwigate.ru/api?key=' . $this->getKey() . '&method=' . $this->method;

        if (!empty($this->options)) {
            $link .= '&' . http_build_query($this->options);
        }

        return $link;
    }

    protected function getKey()
    {
        if ($this->type === 'qiwi') {
            return $this->qiwiKey;
        }

        return $this->accountKey;
    }

    public function getJsonResponse()
    {
        return $this->jsonResponse;
    }

}