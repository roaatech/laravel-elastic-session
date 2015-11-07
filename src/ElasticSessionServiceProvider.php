<?php

namespace ItvisionSy\LaravelElasticSessionDriver;

use Illuminate\Support\ServiceProvider;
use Session;

class ElasticSessionServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        Session::extend('elastic', function($app) {
            $errorReporting = error_reporting();
            error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
            $sessionManager = new ElasticSessionStore($app->make('config')->get('session.elastic'));
            error_reporting($errorReporting);
            return $sessionManager;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
