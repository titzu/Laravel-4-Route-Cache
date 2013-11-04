<?php

namespace Titzu\RouterCache\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ClearCacheCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'router:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear router cache';

    /**
     * Cache instance.
     *
     */
    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($cache) {
        parent::__construct();

        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire() {
        $this->deleteRouterCache($this->cache->driver());
    }

    /**
     * Delete all cache entries found under 'cached_routes' key
     *
     * @return void
     */
    private function deleteRouterCache($cache_driver) {

        if ($cache_driver->has('cached_routes')) {
            $cached_routes = unserialize($cache_driver->get('cached_routes'));
        } else {
            $this->info("Nothing to delete");
            return;
        }

        foreach ($cached_routes as $route) {
            $cache_driver->forget($route);
        }
        $cache_driver->forget('cached_routes');
        $this->info('Router cache cleared');
    }

}