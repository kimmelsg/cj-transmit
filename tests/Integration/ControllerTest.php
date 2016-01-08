<?php

namespace NavJobs\LaravelApi\Test\Integration;

use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use ReflectionClass;
use ReflectionMethod;

class ControllerTest extends TestCase
{

    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->controller = new ReflectionClass(TestController::class);
    }

    /**
     * @test
     */
    public function it_gets_status_codes()
    {
        $method = new ReflectionMethod(
            TestController::class, 'getStatusCode'
        );

        $method->setAccessible(TRUE);

        $this->assertEquals(
            200, $method->invoke(new TestController())
        );
    }

    /**
     * @test
     */
    public function it_can_set_status_codes()
    {
        $setStatusCode = $this->getMethod('setStatusCode');
        $getStatusCode = $this->getMethod('getStatusCode');

        $testController = new TestController();
        $setStatusCode->invokeArgs($testController, [400]);

        $this->assertEquals(
            400, $getStatusCode->invoke($testController)
        );
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_single_item()
    {
        $respondWithItem = $this->getMethod('respondWithItem');
        $testController = new TestController();

        $response = $respondWithItem->invokeArgs($testController, [$this->testBooks[0], new TestTransformer()]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = ['data' => [
            'id' => 1, 'author' => 'Philip K Dick', ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_item_created()
    {
        $respondWithItemCreated = $this->getMethod('respondWithItemCreated');
        $getStatusCode = $this->getMethod('getStatusCode');
        $testController = new TestController();

        $response = $respondWithItemCreated->invokeArgs($testController, [$this->testBooks[0], new TestTransformer()]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = ['data' => [
            'id' => 1, 'author' => 'Philip K Dick', ]];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals(
            201, $getStatusCode->invoke($testController)
        );
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_collection()
    {
        $respondWithCollection = $this->getMethod('respondWithCollection');
        $testController = new TestController();

        $response = $respondWithCollection->invokeArgs($testController, [$this->testBooks, new TestTransformer()]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = ['data' => [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_paginated_collection()
    {
        $respondWithPaginatedCollection = $this->getMethod('respondWithPaginatedCollection');
        $testController = new TestController();

        $response = $respondWithPaginatedCollection->invokeArgs($testController, [collect($this->testBooks), new TestTransformer()]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'data' => [
                ['id' => 1, 'author' => 'Philip K Dick'],
                ['id' => 2, 'author' => 'George R. R. Satan'],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 2,
                    'count' => 2,
                    'per_page' => 10,
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => []
                ]
            ]
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_get_query_parameters()
    {
        $getQueryParameters = $this->getMethod('getQueryParameters');
        $testController = new TestController();

        $_GET = ['page' => 1, 'include' => 'test'];

        $parameters = $getQueryParameters->invoke($testController);

        $this->assertEquals(['include' => 'test'], $parameters);
    }

    /**
     * @param $methodName
     * @return mixed
     */
    protected function getMethod($methodName)
    {
        $method = $this->controller->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
