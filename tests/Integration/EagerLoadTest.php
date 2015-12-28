<?php

namespace NavJobs\LaravelApi\Test\Integration;

class EagerLoadTest extends TestCase
{
    /**
 * @test
 */
    public function it_correctly_parses_a_string_of_includes()
    {
        $transformer = new TestTransformer();
        $array = $transformer->getEagerLoads('characters.test,books,publisher');

        $expectedArray = [
            'characters',
            'publisher'
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_correctly_parses_an_array_of_includes()
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
    public function it_returns_nothing_when_no_matches()
    {
        $transformer = new TestTransformer();
        $array = $transformer->getEagerLoads('papers, books,staplers');
        $expectedArray = [];

        $this->assertEquals($expectedArray, $array);
    }
}
