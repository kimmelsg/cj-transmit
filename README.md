# Laravel Api
An API package for Laravel. Utilizing [laravel-fractal](https://github.com/spatie/laravel-fractal/tree/master/src).

## Api

API Controller documentation coming soon.

## Fractal

Using Fractal data can be transformed like this:

```php
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

$books = [
   ['id'=>1, 'title'=>'Hogfather', 'characters' => [...]], 
   ['id'=>2, 'title'=>'Game Of Kill Everyone', 'characters' => [...]]
];

$manager = new Manager();

$resource = new Collection($books, new BookTransformer());

$manager->parseIncludes('characters');

$manager->createData($resource)->toArray();
```

This package makes that process a tad easier:

```php
$fractal = new Fractal();

$fractal
   ->collection($books)
   ->transformWith(new BookTransformer())
   ->includeCharacters()
   ->toArray();
```

## Install

You can pull in the package via composer:
``` bash
$ composer require NavJobs/laravel-api
```

Next up, the service provider must be registered:

```php
// Laravel5: config/app.php
'providers' => [
    ...
    NavJobs\LaravelApi\FractalServiceProvider::class,

];
```

If you want to change the default serializer, you must publish the config file:

```bash
php artisan vendor:publish --provider="NavJobs\LaravelApi\LaravelApiServiceProvider"
```

This is the contents of the published file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Default Serializer
    |--------------------------------------------------------------------------
    |
    | The default serializer to be used when performing a transformation. It
    | may be left empty to use Fractal's default one. This can either be a
    | string or a League\Fractal\Serializer\SerializerAbstract subclass.
    |
    */

    'default_serializer' => '',

];
```

## Usage

In the following examples were going to use the following array as example input:

```php
$books = [['id'=>1, 'title'=>'Hogfather'], ['id'=>2, 'title'=>'Game Of Kill Everyone']];
```

But know that any structure that can be looped (for instance a collection) can be used.

Let's start with a simple transformation.

```php
$fractal
   ->collection($books)
   ->transformWith(function($book) { return ['id' => $book['id']];})
   ->toArray();
``` 

This will return:
```php
['data' => [['id' => 1], ['id' => 2]]
```

Instead of using a closure you can also pass [a Transformer](http://fractal.thephpleague.com/transformers/):

```php
$fractal
   ->collection($books)
   ->transformWith(new BookTransformer())
   ->toArray();
```

To make your code a bit shorter you could also pass the transform closure or class as a 
second parameter of the `collection`-method:

```php
$fractal->collection($books, new BookTransformer())->toArray();
```

Want to get some sweet json output instead of an array? No problem!
```php
$fractal->collection($books, new BookTransformer())->toJson();
```

A single item can also be transformed:
```php
$fractal->item($books[0], new BookTransformer())->toArray();
```

##Using a serializer

Let's take a look again a the output of the first example:

```php
['data' => [['id' => 1], ['id' => 2]];
```

Notice that `data`-key? That's part of Fractal's default behaviour. Take a look at
[Fractals's documentation on serializers](http://fractal.thephpleague.com/serializers/) to find out why that happens.

If you want to use another serializer you can specify one with the `serializeWith`-method.
The `Spatie\Fractal\ArraySerializer` comes out of the box. It removes the `data` namespace for
both collections and items.

```php
$fractal
   ->collection($books)
   ->transformWith(function($book) { return ['id' => $book['id']];})
   ->serializeWith(new \Spatie\Fractal\ArraySerializer())
   ->toArray();

//returns [['id' => 1], ['id' => 2]]
```

### Changing the default serializer

You can change the default serializer by providing the classname or an instantiation of your favorite serializer in
the config file.

## Using includes

Fractal provides support for [optionally including data](http://fractal.thephpleague.com/transformers/) on the relationships for
the data you're exporting. You can use Fractal's `parseIncludes` which accepts a string or an array:

```php
$fractal
   ->collection($this->testBooks, new TestTransformer())
   ->parseIncludes(['characters', 'publisher'])
   ->toArray();
```

To improve readablity you can also a function named `include` followed by the name
of the include you want to... include:

```php
$fractal
   ->collection($this->testBooks, new TestTransformer())
   ->includeCharacters()
   ->includePublisher()
   ->toArray();
```

## Eager Loads

Fractal lazy loads all includes by default, by extending NavJobs\LaravelApi\Transformer you can easily eager load instead:

```php
$transformer = new UserTransformer;

$eagerloads = $transformer->getEagerLoads(request('include'));

$books = Book::with($eagerLoads)->get();
```

## Including meta data

Fractal has support for including meta data. You can use `addMeta` which accepts 
one or more arrays:

```php
$fractal
   ->collection($this->testBooks, function($book) { return ['name' => $book['name']];})
   ->addMeta(['key1' => 'value1'], ['key2' => 'value2'])
   ->toArray();
```

This will return the following array:

```php
[
   'data' => [
        ['title' => 'Hogfather'],
        ['title' => 'Game Of Kill Everyone'],
    ],
   'meta' => [
        ['key1' => 'value1'], 
        ['key2' => 'value2'],
];
```

## Using pagination

Fractal provides a Laravel-specific paginator, `IlluminatePaginatorAdapter`, which accepts an instance of Laravel's `LengthAwarePaginator`
and works with paginated Eloquent results. When using some serializers, such as the `JsonApiSerializer`, pagination data can be
automatically generated and included in the result set:

```php
$paginator = Book::paginate(5);
$books = $paginator->getCollection();

$fractal
    ->collection($books, new TestTransformer())
    ->serializeWith(new JsonApiSerializer())
    ->paginateWith(new IlluminatePaginatorAdapter($paginator))
    ->toArray();
```

## Setting a custom resource name

Certain serializers wrap the array output with a `data` element. The name of this element can be customized:

```php
$fractal
    ->collection($this->testBooks, new TestTransformer())
    ->serializeWith(new JsonApiSerializer())
    ->resourceName('books')
    ->toArray();
```

```php
$fractal
    ->item($this->testBooks[0], new TestTransformer(), 'book')
    ->serializeWith(new JsonApiSerializer())
    ->toArray();
```

## Testing

``` bash
$ composer test
```

## Credits

- [The League of Extraordinary Packages](http://fractal.thephpleague.com/)
- [Spatie](https://spatie.be/)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
