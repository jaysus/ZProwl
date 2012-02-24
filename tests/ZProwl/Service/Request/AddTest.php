<?php

class ZProwl_Service_Request_AddTest
    extends PHPUnit_Framework_TestCase
{
    protected function _getRequest()
    {
        require_once 'Zend/Config/Ini.php';
        require_once 'ZProwl/Service/Request/Add.php';

        $config  = new Zend_Config_Ini(CONFIG, APPLICATION_ENV);
        $request = new ZProwl_Service_Request_Add();

        $request->setConfigs($config->service);

        return $request;
    }

    private function _getMockHttpClient($response)
    {
        require_once 'Zend/Http/Response.php';

        $client = $this->getMock('Zend_Http_Client');

        $client->expects($this->once())
               ->method('request')
               ->will(
                   $this->returnValue(
                       new Zend_Http_Response(200, array(), $response)
                   )
        );

        return $client;
    }

    public function testExecute()
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<success code="200" '
                  . 'remaining="80" '
                  . 'resetdate="123456" />'
                  . '</prowl>';
        $request  = $this->_getRequest();

        $request->setHttpClient(
            $this->_getMockHttpClient($response)
        );

        $request->setEvent('Event name')
                ->setDescription('Event description')
                ->setPriority(-2)
                ->setAttachementUrl('http://google.com')
                ->setApplication('Application');

        $response = $request->execute();

        $this->assertEquals(80, $response->getRemaining());
        $this->assertEquals(123456, $response->getResetDate());
    }

    public function testExecute_InvalidXml_ThrowExcetption()
    {
        $response = '';
        $request  = $this->_getRequest();

        $request->setHttpClient(
            $this->_getMockHttpClient($response)
        );

        $this->setExpectedException('ZProwl_Service_Exception');

        $request->execute();
    }

    public function testExecute_RequestFail_ThrowException()
    {
        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<error code="400">message</error>'
                  . '</prowl>';
        $request  = $this->_getRequest();

        $request->setHttpClient(
            $this->_getMockHttpClient($response)
        );

        $this->setExpectedException('ZProwl_Service_Exception');

        $request->execute();
    }

    public function testSetPriority_Invalid_ThrowException()
    {
        $request = $this->_getRequest();

        $this->setExpectedException('ZProwl_Service_Exception');

        $request->setPriority(-3);
    }
}
