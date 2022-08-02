<?php

namespace Joaovdiasb\LaravelPdfManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelPdfManagerServiceProvider extends ServiceProvider
{
    protected string $root;

    public function __construct($app) {
        parent::__construct($app);
        $this->root = realpath(__DIR__ . '/../');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom("{$this->root}/config/pdf-manager.php", 'laravel-pdf-manager');
        $this->publishConfig();
//        $this->publishMigration();
        $this->loadViewsFrom("{$this->root}/resources/views", 'laravel-pdf-manager');
        // $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom("{$this->root}/Http/routes.php");
        });
    }

    /**
     * Get route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace'  => "Joaovdiasb\LaravelPdfManager\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register facade
        $this->app->singleton('laravel-pdf-manager', function () {
            return new LaravelPdfManagerFacade();
        });
    }

    /**
     * Publish migration.
     *
     * @return void
     */
    public function publishMigration(): void
    {
        if ($this->app->runningInConsole()) {
            if (!class_exists('app\database\migrations\tenant\CreateDocumentsTable')) {
                $this->publishes([
                    "{$this->root}/database/migrations/create_documents_table.php.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_documents_table.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Publish config.
     *
     * @return void
     */
    public function publishConfig(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                "{$this->root}/config/pdf-manager.php" => config_path('pdf-manager.php'),
            ], 'config');
        }
    }
}
