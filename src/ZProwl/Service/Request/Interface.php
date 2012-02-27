<?php

/**
 * ZProwl
 *
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */

/**
 * @category ZProwl
 * @package  ZProwl_Service
 * @author   Jérémie Havret <jeremie.havret@gmail.com>
 */
interface ZProwl_Service_Request_Interface
{
    /**
     * Execute prowl request
     *
     * @return void
     *
     * @return Zend_Http_Response
     */
    public function execute();

    /**
     * Returns HTTP request method
     *
     * @return string
     */
    public function getMethod();
}
