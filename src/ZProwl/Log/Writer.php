<?php

require_once 'Zend/Log/Writer/Abstract.php';

class ZProwl_Log_Writer
    extends Zend_Log_Writer_Abstract
{
    protected $_request;

    protected function _write($event)
    {
        foreach ($event as $name => $value) {
            switch ($name) {
                case 'message' :
                    $this->_request->setEvent($value);
                    break;
                case 'description' :
                case 'info' :
                    $this->_request->setDescription($value);
                    break;
                case 'application' :
                    $this->_request->setApplication($value);
                    break;
                case 'attachementUrl' :
                    $this->_request->setAttachementUrl($value);
                    break;
            }
        }

        try {
            $this->_request->execute();
        }
        catch (Exception $e) {
            require_once 'ZProwl/Log/Exception.php';

            throw new ZProwl_Log_Exception(
                $e->getMessage(), $e->getCode(), $e
            );
        }
    }

    public static function factory($config)
    {
        require_once 'ZProwl/Service/Request/Add.php';

        $writer = new self();
        $request = new ZProwl_Service_Request_Add();

        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        else if (!is_array($config)) {
            require_once 'ZProwl/Log/Exception.php';

            throw new ZProwl_Log_Exception(
                __class__ . '::' . __function__ . ' parameter 1 excpect to be ' .
                'an instance of Zend_Config or array'
            );
        }

        if (isset($config['service'])) {
            $request->setOptions($config['service']);
        }

        return $writer->setRequest($request);
    }

    public function setRequest(ZProwl_Service_Request_Add $request)
    {
        $this->_request = $request;

        return $this;
    }
}
