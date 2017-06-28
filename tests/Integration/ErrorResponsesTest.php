<?php

namespace NavJobs\Transmit\Test\Integration;

use ReflectionClass;

class ErrorResponsesTest extends TestCase
{

    protected $controller;

    public function setUp($defaultSerializer = '')
    {
        parent::setUp();

        $this->controller = new ReflectionClass(TestController::class);
    }

    /**
     * @test
     */
    public function it_can_respond_with_error_forbidden()
    {
        $errorForbidden = $this->getMethod('errorForbidden');
        $testController = new TestController();

        $response = $errorForbidden->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 403,
                'message' => 'Forbidden'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('403', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_an_internal_error()
    {
        $errorInternalError = $this->getMethod('errorInternalError');
        $testController = new TestController();

        $response = $errorInternalError->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 500,
                'message' => 'Internal Error'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('500', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_not_found_error()
    {
        $errorNotFound = $this->getMethod('errorNotFound');
        $testController = new TestController();

        $response = $errorNotFound->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 404,
                'message' => 'Resource Not Found'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('404', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_unauthorized_error()
    {
        $errorUnauthorized = $this->getMethod('errorUnauthorized');
        $testController = new TestController();

        $response = $errorUnauthorized->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 401,
                'message' => 'Unauthorized'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('401', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_an_unprocessable_entity()
    {
        $errorUnprocessableEntity = $this->getMethod('errorUnprocessableEntity');
        $testController = new TestController();

        $response = $errorUnprocessableEntity->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 422,
                'message' => 'Unprocessable Entity'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('422', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_wrong_arguments_error()
    {
        $errorWrongArgs = $this->getMethod('errorWrongArgs');
        $testController = new TestController();

        $response = $errorWrongArgs->invoke($testController);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 400,
                'message' => 'Wrong Arguments'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('400', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_custom_error()
    {
        $errorCustomType = $this->getMethod('errorCustomType');
        $testController = new TestController();

        $response = $errorCustomType->invokeArgs($testController, ['Test error', 402]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'http_code' => 402,
                'message' => 'Test error'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('402', $response->status());
    }

    /**
     * @test
     */
    public function it_can_respond_with_an_array_of_errors()
    {
        $errorArray = $this->getMethod('errorArray');
        $testController = new TestController();

        $response = $errorArray->invokeArgs($testController, [['field_name' => 'This field_name had an error.'], 422]);
        $array = json_decode(json_encode($response->getData()), true);

        $expectedArray = [
            'errors' => [
                'field_name' => 'This field_name had an error.'
            ]
        ];

        $this->assertEquals($expectedArray, $array);
        $this->assertEquals('422', $response->status());
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
