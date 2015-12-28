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
                '1' => '1'
            ],
            'order' => [
                'created_at' => 'desc'
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
                '1' => '1'
            ]
        ]));
    }
}
