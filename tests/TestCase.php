<?php

namespace Bvtterfly\LaravelHashids\Tests;

use Bvtterfly\LaravelHashids\LaravelHashidsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelHashidsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-hashids_table.php.stub';
        $migration->up();
        */
    }
}
