<?php

namespace NavJobs\LaravelApi\Test\Integration;

use League\Fractal\ParamBag;

class TransformerTest extends TestCase
{
    /**
 * @test
 */
    public function it_correctly_parses_a_string_of_eager_load_includes()
    {
        $transformer = new TestTransformer();
        $array = $transformer->getEagerLoads('characters,books,publisher');

        $expectedArray = [
            'characters',
            'publisher'
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_correctly_parses_an_array_of_eager_load_includes()
    {
        $transformer = new TestTransformer();
        $array = $transformer->getEagerLoads(['characters', 'books', 'publisher']);

        $expectedArray = [
            'characters',
            'publisher'
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_returns_nothing_when_no_eager_load_matches()
    {
        $transformer = new TestTransformer();
        $array = $transformer->getEagerLoads('papers, books,staplers');
        $expectedArray = [];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_accepts_valid_parameters()
    {
        $transformer = new TestTransformer();
        $valid = $transformer->validateParameters(new ParamBag([
            'limit' => [
                0 => '1',
                1 => '1'
            ],
            'sort' => [
                0 => 'created_at',
                1 => 'desc'
            ]
        ]));

        $this->assertEquals(true, $valid);
    }

    /**
     * @test
     * @expectedException NavJobs\LaravelApi\Exceptions\InvalidParameters
     */
    public function it_throws_an_exception_for_invalid_parameters()
    {
        $transformer = new TestTransformer();
        $transformer->validateParameters(new ParamBag([
            'not-valid' => [
                0 => '1'
            ]
        ]));
    }

    /**
     * @test
     */
    public function it_applies_parameters_to_a_query_builder()
    {
        $builder = new TestBuilder();

        $transformer = new TestTransformer();
        $builder = $transformer->applyParameters($builder, new ParamBag([
            'limit' => [
                0 => '5',
                1 => '2'
            ],
            'sort' => [
                0 => 'created_at',
                1 => 'desc'
            ]
        ]));

        $this->assertEquals(5, $builder->getLimit());
        $this->assertEquals(2, $builder->getOffset());
        $this->assertEquals('created_at', $builder->getSort());
        $this->assertEquals('desc', $builder->getOrder());
    }
}
