<?php namespace Titzu\RouterCache;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Router extends \Illuminate\Routing\Router {
    
    public function __construct(Container $container = null)
    {  
        parent::__construct($container);  
    }
    
    /**
     * Register cache_get filter with the router
     *
     * @return void
     */
    public function registerFilterCacheGet()
    {
        //this is to fix the fact that $this is not accesible in php prior to version 5.4
        $self = $this; 
        $this->filter('cache_get', function($route, $request, $response = null) use ($self) {
            $key = $self::getCacheKey($request);
            $container = $self->getContainer();
            return $self::setResponseFromCache($container["cache"]->driver(), $key);
        });
    }
    
    /**
     * Register cache_set filter with the router
     *
     * @return void
     */
    public function registerFilterCacheSet()
    {
        //this is to fix the fact that $this is not accesible in php prior to version 5.4
        $self = $this; 
        $this->filter('cache_set', function($route, $request, $response = null) use ($self) {
            $key = $self::getCacheKey($request);   
            $container = $self->getContainer();
            $self::setCacheFromResponse($response ,$container["cache"]->driver() , $key);
        });
    }
      
    
    /**
     * Register cache_set filter with the router
     *
     * @param Request $request The request instance.
     * 
     * @return string
     */
    public static function getCacheKey(\Illuminate\Http\Request $request)
    {
        return 'route-' . Str::slug($request->url());
    }
    
    /**
     * Sets application response from cache 
     * and also sets the header key Served-From in order to determine that 
     * the content is served from cache
     *
     * @param Repository $cache_driver A cache driver instance.
     * @param string $key The cache key to fecth.
     * 
     * @return Response
     */
    public static function setResponseFromCache(\Illuminate\Cache\Repository $cache_driver, $key)
    {
        if ($cache_driver->has($key)) {
            $response = new Response();

            $response->header('Served-From', 'cache');

            return $response->setContent($cache_driver->get($key));
        }
    }
    
    /**
     * Sets a cache key=>value from the application response 
     * and also sets the header key Served-From in order to determine that 
     * the content is served from the application
     *
     * @param Response $response The response instance.
     * @param Repository $cache_driver A cache driver instance.
     * @param string $key The cache key to set.
     * 
     * @return void
     */
    public static function setCacheFromResponse(\Illuminate\Http\Response $response, \Illuminate\Cache\Repository $cache_driver, $key)
    {

        $response->header('Served-From', 'laravel');

        $cache_driver->put($key, $response->getContent(), 30);

        //workaround to fix the lack of cache sections implementation in file and database cache drivers
        if ($cache_driver->has('cached_routes')) {
           $cr = unserialize($cache_driver->get('cached_routes'));
        } 

        $cr[] = $key;
        $cache_driver->put('cached_routes', serialize($cr), 30000);
    }
}