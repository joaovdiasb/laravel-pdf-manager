<?php

namespace Joaovdiasb\LaravelMultiTenancy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Joaovdiasb\LaravelMultiTenancy\LaravelDocumentManagerServiceProvider;
use Joaovdiasb\LaravelMultiTenancy\Traits\MultitenancyConfig;

abstract class TestCase extends Orchestra
{
    use MultitenancyConfig;

    public function setup(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->setUpDatabase();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->bind('DatabaseSeeder', 'Joaovdiasb\LaravelMultiTenancy\Tests\MockDatabaseSeeder');
        $app['config']->set('multitenancy', [
            'encrypt_key'              => '318654690878bef944a8b542ddb55d82',
            'database'                 => 'mysql',
            'current_container_key'    => 'currentTenant',
            'tenant_connection_name'   => 'tenant',
            'landlord_connection_name' => 'landlord',
        ]);
        $app['config']->set($this->landlordConnectionFullName(), [
            'driver'   => env('DB_DRIVER'),
            'database' => env('DB_DATABASE'),
            'host'     => env('DB_HOST'),
            'port'     => env('DB_PORT'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD')
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [LaravelDocumentManagerServiceProvider::class];
    }

    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../database/migrations/create_documents_table.php.stub';
        (new \CreateTenantsTable())->down();
        (new \CreateTenantsTable())->up();
    }
}
