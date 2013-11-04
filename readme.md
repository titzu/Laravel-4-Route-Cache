This is a Laravel 4 package that provides caching for the response of the application.

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `titzu/cache`.

    "require": {
            "laravel/framework": "4.0.*",
            "titzu/cache": "dev-master"
    },
    "require-dev": {
            "phpunit/phpunit": "3.7.*",
            "mockery/mockery": "dev-master@dev"
    },
    "minimum-stability" : "dev"

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Titzu\RouterCache\RouterCacheServiceProvider'

That's all!

### Usage

## Register route for caching

You must register the filter with the route
  
```php
Route::get('/', array('before' => 'cache_get', 'after' => 'cache_set', 'uses' => 'HomeController@show'));
```

Of course you can also register a group of routes

```php
Route::group(array('before' => 'cache_get', 'after' => 'cache_set'), function()
{
    Route::get('/', 'HomeController@show');
    Route::get('another', 'AnotherController@show');
});
```

## Clear Routes Cache

    php artisan router:clear

By default Laravel uses file cache driver for cache storage. Because cache sections are not supported when using the file or database cache drivers
and in order not to flush all cache when we need to refresh just the routes cache I keep cached routes in a separate cache entry 'cached_routes'.
The router:clear command will then only operate on the keys registered in cached_routes array. 

## Note

In order to verify that the request is served from the cache or on the fly, I added a new header entry

```php
$response->header('Served-From', 'cache');
```

and 

```php
$response->header('Served-From', 'laravel');
```

You can then find this using entry developer tools ( firebug for example ).

## Contact

Feel free to use, fork, whatever :) 