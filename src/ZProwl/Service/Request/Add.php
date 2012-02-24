<?php

require_once 'ZProwl/Service/Request/Abstract.php';

class ZProwl_Service_Request_Add
    extends ZProwl_Service_Request_Abstract
{
    /**
     * Available message priorities
     */
    const PRIORITY_VERY_LOW  = -2;
    const PRIORITY_MODERATE  = -1;
    const PRIORITY_NORMAL    = 0;
    const PRIORITY_HIGH      = 1;
    const PRIORITY_EMERGENCY = 2;

    /**
     * Event to push
     *
     * @var string
     */
    protected $_event;

    /**
     * Event description
     *
     * @var string
     */
    protected $_description;

    /**
     * Event priority
     *
     * @var inteer
     */
    protected $_priority;

    /**
     * Event attachement url
     * (optionnal)
     *
     * @var string
     */
    protected $_attachementUrl;

    /**
     * Event appliction name
     * (optionnal)
     *
     * @var string
     */
    protected $_application;

    /**
     * Init request
     *
     * @return ZProwl_Service_Request_Add
     */
    public function init()
    {
        parent::init();

        $this->_type = self::REQUEST_ADD;

        $this->_setParameter('priority', $this->_priority)
             ->_setParameter('url', $this->_attachementUrl)
             ->_setParameter('application', $this->_application)
             ->_setParameter('event', $this->_event)
             ->_setParameter('description', $this->_description);
    }

    /**
     * Exceute prowl request
     *
     * @return ZProwl_Service_Response
     *
     * @throw ZProwl_Service_Exception if request failed
     */
    public function execute()
    {
        $response = parent::execute();

        require_once 'ZProwl/Service/Response.php';

        $response = new ZProwl_Service_Response($response->getBody());

        if (!$response->success()) {
            require_once 'ZProwl/Service/Exception.php';

            throw new ZProwl_Service_Exception(
                $response->getErrorMessage(),
                $response->getErrorCode()
            );
        }

        return $response;
    }

    /**
     * Returns request method
     *
     * @return string
     */
    public function getMethod()
    {
        return self::METHOD_POST;
    }

    /**
     * Set event name
     *
     * @param string $value
     *
     * @return ZProwl_Service_Request_Add
     */
    public function setEvent($value)
    {
        $this->_event = $value;

        return $this;
    }

    /**
     * Set event description
     *
     * @param string $value
     *
     * @return ZProwl_Service_Request_Add
     */
    public function setDescription($value)
    {
        $this->_description = $value;

        return $this;
    }

    /**
     * Set event priority
     *
     * @param string $value
     *
     * @return ZProwl_Service_Request_Add
     *
     * @throw Prowl_Service_Exception if priority is not valid
     */
    public function setPriority($value)
    {
        switch ($value) {
            case self::PRIORITY_EMERGENCY :
            case self::PRIORITY_HIGH      :
            case self::PRIORITY_NORMAL    :
            case self::PRIORITY_MODERATE  :
            case self::PRIORITY_VERY_LOW  :
                break;
            default  :
                require_once 'ZProwl/Service/Exception.php';
                throw new ZProwl_Service_Exception(
                    'Invalid priority'
                );
        }

        $this->_priority = $value;

        return $this;
    }

    /**
     * Set event attachement utl
     *
     * @param string $value
     *
     * @return ZProwl_Service_Request_Add
     */
    public function setAttachementUrl($value)
    {
        $this->_attachementUrl = $value;

        return $this;
    }

    /**
     * Set event application
     *
     * @param string $value
     *
     * @return ZProwl_Service_Request_Add
     */
    public function setApplication($value)
    {
        $this->_application = $value;
    }
}
