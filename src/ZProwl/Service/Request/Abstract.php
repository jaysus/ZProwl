<?php

/**
 * ZProwl
 *
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */

require_once 'Zend/Service/Abstract.php';
require_once 'ZProwl/Service/Request/Interface.php';

/**
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */
abstract class ZProwl_Service_Request_Abstract
    extends    Zend_Service_Abstract
    implements ZProwl_Service_Request_Interface
{
    /**#@+
     * Available message priorities
     * Available request methods
     */
    const METHOD_GET  = 'GET';
    const METHOD_POST = 'POST';

    /**#@+
     * Available request types
     */
    const REQUEST_ADD = 'add';

    /**#@+
     * Available request types
     */
    const BASE_URL =  'https://api.prowlapp.com/publicapi';

    /**
     * Prowl apikey
     *
     * @var string
     */
    protected $_apiKey;

    /**
     * Prowl provider key, auto detect application name
     * (optionnal)
     *
     * @var string
     */
    protected $_providerKey;

    /**
     * Request type
     *
     * @var string
     */
    protected $_type;

    /**
     * Execute prowl request
     *
     * @return void
     *
     * @return Zend_Http_Response
     */
    public function execute()
    {
        $this->init();

        if ($this->getMethod() == 'POST') {
            self::getHttpClient()->setEncType();
        }

        $uri = self::BASE_URL . '/' . $this->_type;

        self::getHttpClient()->setUri($uri);

        return self::getHttpClient()->request($this->getMethod());
    }

    /**
     * Init request, set default parameters
     *
     * @return void
     */
    public function init()
    {
        $this->_setParameter('apikey', $this->_apiKey);
        $this->_setParameter('providerkey', $this->_providerKey);
    }

    /**
     * Set service config
     *
     * @param Zend_Config $config
     *
     * @return ZProwl_Service_Request_Abstract
     */
    public function setConfigs(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set service options
     *
     * @param array $options
     *
     * @return ZProwl_Service_Request_Abstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Set service option
     *
     * @param string         $option Option name
     * @param string|integer $value Option value
     *
     * @return ZProwl_Service_Request_Abstract
     */
    public function setOption($option, $value)
    {
        $attribute = '_' . $option;

        if (property_exists($this, $attribute)) {
            $this->$attribute = $value;

        }

        return $this;
    }

    /**
     * Set request parameter
     *
     * @param string         $name Parameter option
     * @param string|integer $value Parameter value
     *
     * @return ZProwl_Service_Request_Abstract
     */
    protected function _setParameter($name, $value)
    {
        switch ($this->getMethod()) {
            /*
            case self::METHOD_GET :
                self::getHttpClient()->setParameterGet($name, $value);
                break;
             */
            case self::METHOD_POST :
                self::getHttpClient()->setParameterPost($name, $value);
                break;
        }

        return $this;
    }
}
