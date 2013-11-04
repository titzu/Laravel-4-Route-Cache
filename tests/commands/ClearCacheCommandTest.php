<?php

use Titzu\RouterCache\Commands\ClearCacheCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ClearCacheCommandTest extends TestCase {
    
    public function tearDown()
    {
        m::close();
    }

    public function testClearCacheWithNoCacheSet()
    {

        $cache = $this->app['cache']; 
        
        $command = new ClearCacheCommand($cache);
        $tester = new CommandTester($command);
        $tester->execute(array());

        $this->assertContains("Nothing to delete", $tester->getDisplay());
        
    }
    
    public function testClearCache()
    {

        $cache = $this->app['cache']; 
        
        $cache->put('test', 'test route content', 30);
        $cached_routes[] = 'test';
        $cache->put('cached_routes', serialize($cached_routes), 30);
        
        $command = new ClearCacheCommand($cache);
        $tester = new CommandTester($command);
        $tester->execute(array());

        $this->assertContains("Router cache cleared", $tester->getDisplay());
        
    }

}