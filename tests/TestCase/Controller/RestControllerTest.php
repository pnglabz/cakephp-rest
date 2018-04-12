<?php

namespace Rest\Test\TestCase\Controller;

use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Rest\Controller\RestController Test Case
 */
class RestControllerTest extends IntegrationTestCase
{

    public $controller = null;

    /**
     * Setup method.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $request = new \Cake\Http\ServerRequest();
        $response = new \Cake\Http\Response();
        $this->controller = $this->getMockBuilder('Rest\Controller\RestController')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Test beforeRender method
     *
     * @return void
     */
    public function testBeforeRender()
    {
        $this->controller->beforeRender(new Event('Controller.beforeRender'));
        $viewClass = $this->controller->viewBuilder()->className();
        $this->assertEquals('Rest.Json', $viewClass);
    }
}