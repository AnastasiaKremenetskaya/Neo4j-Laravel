<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $client = new \Everyman\Neo4j\Client("neo4j");
        //$client->getTransport()->setAuth("neo4j", "neo4j");

// Test connection to server
        dd($client->getServerInfo());
    }
}
