<?php namespace Titzu\RouterCache;

use Titzu\RouterCache\Router;
use Illuminate\Routing\RoutingServiceProvider;

class RouterCacheServiceProvider extends RoutingServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        
        $this->app['router.clear'] = $this->app->share(function($app) {
            return new Commands\ClearCacheCommand($app['cache']);
        });

        $this->commands(
            'router.clear'
        );
    }

    /**
     * Boot the service provider. We bind our router to the application
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router'] = $this->app->share(function($app) {
            return new \Titzu\RouterCache\Router($app);
        });
        $this->app['router']->registerFilterCacheGet();
        $this->app['router']->registerFilterCacheSet();
        parent::boot();
    }

}