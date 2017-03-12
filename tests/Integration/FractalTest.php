<?php

namespace NavJobs\Transmit\Test\Integration;

use ReflectionClass;
use League\Fractal\Scope;
use League\Fractal\Manager;
use NavJobs\Transmit\Fractal;
use League\Fractal\Resource\ResourceInterface;

class FractalTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_transform_multiple_items_using_a_transformer_to_json()
    {
        $json = $this->fractal
            ->collection($this->testBooks, new TestTransformer())
            ->toJson();

        $expectedJson = '{"data":[{"id":1,"author":"Philip K Dick"},{"id":2,"author":"George R. R. Satan"}]}';

        $this->assertEquals($expectedJson, $json);
    }

    /**
     * @test
     */
    public function it_can_transform_multiple_items_using_a_transformer_to_an_array()
    {
        $array = $this->fractal
            ->collection($this->testBooks, new TestTransformer())
            ->toArray();

        $expectedArray = ['data' => [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_transform_a_collection_using_a_callback()
    {
        $array = $this->fractal
            ->collection($this->testBooks, function ($book) {
                return ['id' => $book['id']];
            })->toArray();

        $expectedArray = ['data' => [
            ['id' => 1],
            ['id' => 2],
        ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_provides_a_method_to_specify_the_transformer()
    {
        $array = $this->fractal
            ->collection($this->testBooks)
            ->transformWith(new TestTransformer())
            ->toArray();

        $expectedArray = ['data' => [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_perform_a_single_item()
    {
        $array = $this->fractal
            ->item($this->testBooks[0], new TestTransformer())
            ->toArray();

        $expectedArray = ['data' => [
            'id' => 1, 'author' => 'Philip K Dick', ]];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_can_create_a_resource()
    {
        $resource = $this->fractal
            ->collection($this->testBooks, new TestTransformer())
            ->getResource();

        $this->assertInstanceOf(ResourceInterface::class, $resource);
    }

    /**
     * @test
     */
    public function it_can_create_fractal_data()
    {
        $resource = $this->fractal
            ->collection($this->testBooks, new TestTransformer())
            ->createData();

        $this->assertInstanceOf(Scope::class, $resource);
    }

    /**
     * @test
     */
    public function it_continues_as_normal_if_no_includes_are_given()
    {
        $this->fractal->parseIncludes(null);

        $this->assertInstanceOf(get_class($this->fractal), $this->fractal->parseIncludeParams());
    }

    /**
     * @test
     */
    public function it_returns_null_for_empty_include_parameters()
    {
        $this->assertEquals(null, $this->fractal->getIncludeParams(null));
    }

    /**
     * @test
     */
    public function it_passes_unknown_method_calls_through_to_fractal_manager()
    {
        $this->assertInstanceOf(Manager::class, $this->fractal->setRecursionLimit(1));
    }

    /**
     * @test
     * @expectedException \ErrorException
     * @expectedExceptionMessage Call to undefined method NavJobs\Transmit\Fractal::nothing()
     */
    public function it_throws_an_error_for_unknown_methods_or_includes()
    {
        $this->fractal->nothing();
    }

    /**
     * @test
     * @expectedException NavJobs\Transmit\Exceptions\InvalidTransformation
     */
    public function it_throws_an_exception_when_passed_an_invalid_data_type()
    {
        $fractal = new ReflectionClass(Fractal::class);
        $method = $fractal->getMethod('data');
        $method->setAccessible(true);

        $method->invokeArgs($this->fractal, ['invalidType', 'invalidItem']);

        $this->fractal->getResource();
    }
}
