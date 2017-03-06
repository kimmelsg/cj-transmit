<?php

namespace NavJobs\Transmit\Test\Integration;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
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
    public function it_can_be_instantiated_with_header_includes()
    {
        request()->headers->add(['include' => 'test']);

        $controller = new TestController();

        $this->assertTrue(isset($controller));
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_with_query_string_includes()
    {
        request()->query->add(['include' => 'test']);

        $controller = new TestController();

        $this->assertTrue(isset($controller));
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
        $testController = $testController->setTransformer(new TestTransformer);

        $response = $respondWithItem->invokeArgs($testController, [$this->testBooks[0]]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = ['data' => [
            'id' => 1, 'author' => 'Philip K Dick', ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_single_item_at_the_top_level()
    {
        $respondWithItem = $this->getMethod('respondWithItem');
        $testController = new TestController();
        $testController = $testController->setTransformer(new TestTransformer)->setResourceKey(false);

        $response = $respondWithItem->invokeArgs($testController, [$this->testBooks[0]]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'id' => 1, 'author' => 'Philip K Dick', ];

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
        $testController = $testController->setTransformer(new TestTransformer);

        $response = $respondWithItemCreated->invokeArgs($testController, [$this->testBooks[0]]);
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
        $testController = $testController->setTransformer(new TestTransformer);

        $expectedData = [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ];

        $response = $respondWithCollection->invokeArgs($testController, [$this->testBooks]);
        $array = json_decode(json_encode($response->getData()), true);

        $this->assertEquals(['data' => $expectedData], $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_collection_as_a_top_level_item()
    {
        $respondWithCollection = $this->getMethod('respondWithCollection');
        $testController = new TestController();
        $testController = $testController->setTransformer(new TestTransformer)->setResourceKey(false);

        $expectedData = [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ];

        $response = $respondWithCollection->invokeArgs($testController, [$this->testBooks]);
        $array = json_decode(json_encode($response->getData()), true);

        $this->assertEquals($expectedData, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_paginated_collection()
    {
        $respondWithPaginatedCollection = $this->getMethod('respondWithPaginatedCollection');
        $testController = new TestController();
        $testController = $testController->setTransformer(new TestTransformer);
        $lengthAwarePaginator = new LengthAwarePaginator($this->testBooks, count($this->testBooks), 10);
        $builder = Mockery::mock(Builder::class);
        $builder->shouldReceive('paginate')->once()->andReturn($lengthAwarePaginator);

        $response = $respondWithPaginatedCollection->invokeArgs($testController, [$builder, 10]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'data' => [
                ['id' => 1, 'author' => 'Philip K Dick'],
                ['id' => 2, 'author' => 'George R. R. Satan'],
            ],
            'pagination' => [
                'total' => 2,
                'count' => 2,
                'per_page' => 10,
                'current_page' => 1,
                'total_pages' => 1,
                'links' => []
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
     * @test
     */
    public function it_can_respond_with_an_array()
    {
        $respondWithArray = $this->getMethod('respondWithArray');
        $testController = new TestController();

        $response = $respondWithArray->invokeArgs($testController, [$this->testBooks[0]]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'id' => 1,
            'title' => 'Hogfather',
            'yr' => '1998',
            'author_name' => 'Philip K Dick',
            'author_email' => 'philip@example.org',
            'characters' => [
                [
                    'name' => 'Death'
                ],
                [
                    'name' => 'Hex'
                ]
            ],
            'publisher' => 'Elephant books'
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_no_content()
    {
        $respondWithNoContent = $this->getMethod('respondWithNoContent');
        $testController = new TestController();

        $response = $respondWithNoContent->invoke($testController);

        $this->assertEquals('204', $response->status());
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
