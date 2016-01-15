<?php

namespace NavJobs\Transmit\Test\Integration;

use NavJobs\Transmit\Serializers\ArraySerializer;
use NavJobs\Transmit\Serializers\DataArraySerializer;

class SerializerTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_generate_a_data_key_for_a_collection()
    {
        $array = $this->fractal
            ->collection($this->testBooks, new TestTransformer())
            ->serializeWith(new ArraySerializer())
            ->toArray();

        $expectedArray = [
            ['id' => 1, 'author' => 'Philip K Dick'],
            ['id' => 2, 'author' => 'George R. R. Satan'],
        ];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_does_not_generate_a_data_key_for_an_item()
    {
        $array = $this->fractal
            ->item($this->testBooks[0], new TestTransformer())
            ->serializeWith(new ArraySerializer())
            ->toArray();

        $expectedArray = ['id' => 1, 'author' => 'Philip K Dick'];

        $this->assertEquals($expectedArray, $array);
    }

    /**
     * @test
     */
    public function it_returns_a_null_resource_for_a_null_item()
    {
        $array = $this->fractal
            ->item($this->testBooks[0], new TestTransformer())
            ->parseIncludes(['test'])
            ->serializeWith(new DataArraySerializer())
            ->toArray();


        $expectedArray = [
            'data' => [
                'id' => 1,
                'author' => 'Philip K Dick',
                'test' => [
                    'data' => null
                ]
            ]
        ];

        $this->assertEquals($expectedArray, $array);
    }
}
