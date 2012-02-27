<?php

/**
 * ZProwl
 *
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */

require_once 'Zend/Http/Response.php';

/**
 * ZProwl
 *
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */
class ZProwl_Service_Response
    extends Zend_Http_Response
{
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
     /**
      * HTTP response constructor

      * @param int    $code Response code (200, 404, ...)
      * @param array  $headers Headers array
      * @param string $body Response body
      * @param string $version HTTP version
      * @param string $message Response code as text

      * @throws Zend_Http_Exception
      * @throws ZProwl_Service_Exception
      */
     public function __construct($code, array $headers, $body = null, $version = '1.1', $message = null)
    {
        parent::__construct($code, $headers, $body, $version, $message);

        $xml = @simplexml_load_string($body);

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
    public function isSuccessful()
    {
        if (parent::isSuccessFul() && isset($this->_xml->success)) {
            return true;
        }

        return false;
    }

    /**
     * Returns response error code, null if success
     *
     * @return integer|null
     */
    public function getErrorCode()
    {
        if (!$this->isSuccessful()) {
            return (int)$this->_xml->error->attributes()->code;
        }

        return null;
    }

    /**
     * Returns error message, nullif success
     *
     * @return string|null
     */
    public function getErrorMessage()
    {
        if (!$this->isSuccessful()) {
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
        if ($this->isSuccessful()) {
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
        if ($this->isSuccessful()) {
            return (int)$this->_xml->success->attributes()->resetdate;
        }

        return null;
    }
}
