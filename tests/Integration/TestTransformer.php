<?php

namespace NavJobs\Transmit\Test\Integration;

use NavJobs\Transmit\Transformer;

class TestTransformer extends Transformer
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'characters',
        'publisher',
        'test'
    ];

    /**
     * List of valid parameters.
     *
     * @var array
     */
    protected $validParameters = [
        'limit',
        'sort'
    ];

    /**
     * @param array $book
     *
     * @return array
     */
    public function transform(array $book)
    {
        return [
            'id' => (int) $book['id'],
            'author' => $book['author_name'],
        ];
    }

    /**
     * Include characters.
     *
     * @param array $book
     *
     * @return \League\Fractal\ItemResource
     */
    public function includeCharacters(array $book)
    {
        $characters = $book['characters'];

        return $this->collection($characters, function ($character) {
            return $character['name'];
        });
    }

    /**
     * Include characters.
     *
     * @param array $book
     *
     * @return \League\Fractal\ItemResource
     */
    public function includePublisher(array $book)
    {
        $publisher = $book['publisher'];

        return $this->item([$publisher], function ($publisher) {
            return $publisher;
        });
    }

    /**
     * Include test.
     *
     * @param array $test
     *
     * @return \League\Fractal\ItemResource
     */
    public function includeTest(array $test)
    {
        return $this->item(null, new TestTransformer());
    }
}
