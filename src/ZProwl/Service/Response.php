<?php

class ZProwl_Service_Response
{
    /**
     * Available statuses
     */
    const STATUS_OK  = 1;
    const STATUS_NOK = 0;

    /**
     * Prowl xml response
     *
     * @var simpl_xml_object
     */
    protected $_xml;

    /**
     * Constructor
     *
     * @param string $response
     *
     * @return void
     */
    public function __construct($response)
    {
        $xml = @simplexml_load_string($response);

        if ($xml === false) {
            require_once 'ZProwl/Service/Exception.php';

            throw new ZProwl_Service_Exception('Response invalid xml');
        }

        $this->_xml = $xml;
    }

    /**
     * Returns true request success
     *
     * @return boolean
     */
    public function success()
    {
        return $this->getStatus() == self::STATUS_OK;
    }

    /**
     * Returns response status
     *
     * @return integer
     */
    public function getStatus()
    {
        if (isset($this->_xml->success)) {
            return self::STATUS_OK;
        }

        return self::STATUS_NOK;
    }

    /**
     * Returns response error code, null if success
     *
     * @return integer|null
     */
    public function getErrorCode()
    {
        if (!$this->success()) {
            return (int)$this->_xml->error->attributes()->code;
        }

        return null;
    }

    /**
     * Returns error message, nullif success
     *
     * @returns string|null
     */
    public function getErrorMessage()
    {
        if (!$this->success()) {
            return (string)$this->_xml->error;
        }

        return null;
    }

    /**
     * Retuns remaining messages, null if request fail
     *
     * @return integer|null
     */
    public function getRemaining()
    {
        if ($this->success()) {
            return (int)$this->_xml->success->attributes()->remaining;
        }

        return null;
    }

    /**
     * Returns remaning reset date, null if request fail
     *
     * @return integer|null
     */
    public function getResetDate()
    {
        if ($this->success()) {
            return (int)$this->_xml->success->attributes()->resetdate;
        }

        return null;
    }
}
