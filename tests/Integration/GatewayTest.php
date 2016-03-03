<?php

namespace NavJobs\Transmit\Test\Integration;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\ParameterBag;

class GatewayTest extends TestCase
{

    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ReflectionClass(TestGateway::class);
    }

    /**
     * @test
     */
    public function it_can_be_instantiated()
    {
        $gateway = new TestGateway();

        $this->assertTrue(isset($gateway));
    }

    /**
     * @test
     */
    public function it_gets_status_codes()
    {
        $method = new ReflectionMethod(
            TestGateway::class, 'getStatusCode'
        );

        $method->setAccessible(TRUE);

        $this->assertEquals(
            200, $method->invoke(new TestGateway())
        );
    }

    /**
     * @test
     */
    public function it_can_set_status_codes()
    {
        $setStatusCode = $this->getMethod('setStatusCode');
        $getStatusCode = $this->getMethod('getStatusCode');

        $testGateway = new TestGateway();
        $setStatusCode->invokeArgs($testGateway, [400]);

        $this->assertEquals(
            400, $getStatusCode->invoke($testGateway)
        );
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_single_item()
    {
        $respondWithItem = $this->getMethod('respondWithItem');
        $testGateway = new TestGateway();

        $response = $respondWithItem->invokeArgs($testGateway, [$this->testBooks[0], new TestTransformer()]);
        $array = json_decode($response, true);

        $expectedArray = [
            'data' => [
                'id' => 1,
                'author' => 'Philip K Dick'
            ],
            'status_code' => '200'
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_collection()
    {
        $respondWithCollection = $this->getMethod('respondWithCollection');
        $testGateway = new TestGateway();

        $response = $respondWithCollection->invokeArgs($testGateway, [$this->testBooks, new TestTransformer()]);
        $array = json_decode($response, true);

        $expectedArray = [
            'data' => [
                ['id' => 1, 'author' => 'Philip K Dick'],
                ['id' => 2, 'author' => 'George R. R. Satan']
            ],
            'status_code' => 200
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_parse_parameters()
    {
        $parseParameters = $this->getMethod('parseParameters');
        $testGateway = new TestGateway();

        $parameters = [
            'with' => 'characters, publisher'
        ];

        $response = $parseParameters->invokeArgs($testGateway, [json_encode($parameters)]);

        $this->assertTrue($response instanceof ParameterBag);
        $this->assertEquals('characters, publisher', $response->get('with'));
    }

    /**
     * @test
     */
    public function it_can_respond_with_a_not_found_error()
    {
        $errorNotFound = $this->getMethod('errorNotFound');
        $testGateway = new TestGateway();

        $response = $errorNotFound->invoke($testGateway);
        $array = json_decode($response, true);

        $expectedArray = [
            'error' => [
                'status_code' => 404,
                'message' => 'Resource Not Found'
            ],
            'status_code' => 404
        ];

        $this->assertEquals($expectedArray, $array);
    }


    /**
     * @param $methodName
     * @return mixed
     */
    protected function getMethod($methodName)
    {
        $method = $this->gateway->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }
}
