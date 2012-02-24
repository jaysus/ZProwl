<?php

class ZProwl_Log_WritterTest
    extends PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        require_once 'Zend/Log.php';
        require_once 'Zend/Log/Writer/Mock.php';
        require_once 'ZProwl/Log/Writer.php';
        require_once 'ZProwl/Service/Request/Add.php';
        require_once 'Zend/Config/Ini.php';
        require_once 'ZProwl/Service/Response.php';

        $request  = $this->getMock('ZProwl_Service_Request_Add');
        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<success code="200" '
                  . 'remaining="80" '
                  . 'resetdate="123456" />'
                  . '</prowl>';

        $request->expects($this->once())
                ->method('execute')
                ->will(
                    $this->returnValue(
                        new ZProwl_Service_Response($response)
                    )
        );

        $config   = new Zend_Config_Ini(CONFIG, APPLICATION_ENV);
        $log      = new Zend_Log();
        $logMock  = new Zend_Log_Writer_Mock();
        $prowl    = ZProwl_Log_Writer::factory($config);

        $prowl->setRequest($request);

        $log->addWriter($prowl);
        $log->addWriter($logMock);

        $extra = array(
            'description'    => 'Description',
            'attachementUrl' => 'http://google.com',
            'application'    => 'ApplicationName'
        );

        $log->info('Message', $extra);

        $event = $logMock->events[0];

        $this->assertEquals('Message', $event['message']);
        $this->assertEquals('Description', $event['description']);
        $this->assertEquals('http://google.com', $event['attachementUrl']);
        $this->assertEquals('ApplicationName', $event['application']);
    }

    public function testWrite_RequestFailure_ThrowException()
    {
        require_once 'Zend/Log.php';
        require_once 'Zend/Log/Writer/Mock.php';
        require_once 'ZProwl/Log/Writer.php';
        require_once 'ZProwl/Service/Request/Add.php';
        require_once 'Zend/Config/Ini.php';
        require_once 'ZProwl/Log/Exception.php';

        $request = $this->getMock('ZProwl_Service_Request_Add');

        $request->expects($this->once())
                ->method('execute')
                ->will(
                    $this->throwException(
                        new ZProwl_Log_Exception()
                    )
        );

        $config = new Zend_Config_Ini(CONFIG, APPLICATION_ENV);

        $log      = new Zend_Log();
        $logMock  = new Zend_Log_Writer_Mock();
        $prowl    = ZProwl_Log_Writer::factory($config);
        $response = '<?xml version="1.0" encoding="UTF-8"?>'
                  . '<prowl>'
                  . '<error code="400">message</error>'
                  . '</prowl>';

        $prowl->setRequest($request);

        $log->addWriter($prowl);
        $log->addWriter($logMock);

        $extra = array(
            'description'    => 'Description',
            'attachementUrl' => 'http://google.com',
            'application'    => 'ApplicationName'
        );

        $this->setExpectedException('ZProwl_Log_Exception');

        $log->info('Message', $extra);
    }

    public function testFactory_Invalid_Config()
    {
        $this->setExpectedException('ZProwl_Log_Exception');

        $prowl = ZProwl_Log_Writer::factory('config');
    }
}
