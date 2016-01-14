<?php

namespace NavJobs\Transmit\Test\Integration;

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
}
