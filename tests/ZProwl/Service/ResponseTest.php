<?php

class ZProwl_Service_ResponseTest
    extends PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        require_once 'ZProwl/Service/Response.php';

        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<success code="200" '
                  . 'remaining="80" '
                  . 'resetdate="123456" />'
                  . '</prowl>';
        $response = new ZProwl_Service_Response($response);

        $this->assertEquals(80, $response->getRemaining());
        $this->assertEquals(123456, $response->getResetDate());
        $this->assertEquals(null, $response->getErrorCode());
        $this->assertEquals(null, $response->getErrorMessage());
    }

    public function testNok()
    {
        require_once 'ZProwl/Service/Response.php';

        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<error code="400">message</error>'
                  . '</prowl>';
        $response = new ZProwl_Service_Response($response);

        $this->assertEquals(null, $response->getRemaining());
        $this->assertEquals(null, $response->getResetDate());
        $this->assertEquals(400, $response->getErrorCode());
        $this->assertEquals('message', $response->getErrorMessage());
    }
}
