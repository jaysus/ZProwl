<?php

/**
 * ZProwl
 *
 * @category ZProwl
 * @package  ZProwl_Log
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */

require_once 'Zend/Log/Writer/Abstract.php';

/**
 * @category ZProwl
 * @package  ZProwl_Log
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */
class ZProwl_Log_Writer
    extends Zend_Log_Writer_Abstract
{
    /**
     * Service request
     *
     * @var ZProwl_Service_Request_Add
     */
    protected $_request;

    protected $_priorityAliases = array();

    /*
     * Write a message to the log.
     *
     * @param  array $event log data event
     *
     * @return void
     *
     * @throw ZProwl_Log_Exception if request failed
     */
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

        if ($this->hasPriorityAliases()) {
            $priorityName = $event['priorityName'];

            if (!isset($this->_priorityAliases[$priorityName])) {
                require_once 'ZProwl/Log/Exception.php';

                throw new ZProwl_Log_Exception(
                    'Priotity alias missing for ' . $priorityName
                );
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

    /**
     * Construct a Zend_Log driver
     *
     * @param  array|Zend_Config $config
     *
     * @return ZProwl_Log_Writer
     */
    public static function factory($config)
    {
        require_once 'ZProwl/Service/Request/Add.php';

        $writer  = new self();
        $request = new ZProwl_Service_Request_Add();

        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        else if (!is_array($config)) {
            require_once 'ZProwl/Log/Exception.php';

            throw new ZProwl_Log_Exception(
                __class__ . '::' . __function__ .
                ' parameter 1 excpect to be ' .
                'an instance of Zend_Config or array'
            );
        }

        if (isset($config['service'])) {
            $request->setOptions($config['service']);
        }

        if (isset($config['priorityAliases'])) {
            $writer->setPriorityAliases($config['priorityAliases']);
        }

        return $writer->setRequest($request);
    }

    /**
     * Set request
     *
     * @param ZProwl_Service_Request_Add $request
     *
     * @return ZProwl_Log_Writer
     */
    public function setRequest(ZProwl_Service_Request_Add $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Set priority aliases
     *
     * @param array $aliases
     *
     * @return ZProwl_Log_Writer
     */
    public function setPriorityAliases(array $aliases)
    {
        foreach ($aliases as $zPriority => $pPriority) {
            $this->addPriorityAlias($zPriority, $pPriority);
        }

        return $this;
    }

    /**
     * Add priority alias
     *
     * @param string  $zPriority Zend_Log priority name
     * @param integer $pPriority Prowl priority name
     *
     * @return ZProwl_Log_Writer
     */
    public function addPriorityAlias($zPriority, $pPriority)
    {
        switch ((int)$pPriority) {
            case ZProwl_Service_Request_Add::PRIORITY_VERY_LOW  :
            case ZProwl_Service_Request_Add::PRIORITY_MODERATE  :
            case ZProwl_Service_Request_Add::PRIORITY_NORMAL    :
            case ZProwl_Service_Request_Add::PRIORITY_HIGH      :
            case ZProwl_Service_Request_Add::PRIORITY_EMERGENCY :
                break;
            default :
                require_once 'ZProwl/Log/Exception.php';

                throw new ZProwl_Log_Exception(
                    'Invalid prowl priority'
                );
        }

        $this->_priorityAliases[$zPriority] =  $pPriority;

        return $this;
    }

    /**
     * Returns true if has priority aliases
     *
     * @return boolean
     **/
    public function hasPriorityAliases()
    {
        return count($this->_priorityAliases) > 0;
    }
}
