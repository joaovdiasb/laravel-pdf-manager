<?php

namespace Joaovdiasb\LaravelPdfManager;

use Illuminate\Support\ServiceProvider;

class LaravelPdfManagerServiceProvider extends ServiceProvider
{
    protected string $root;

    public function __construct($app) {
        parent::__construct($app);
        $this->root = dirname(__DIR__) . '/';
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom("{$this->root}/config/pdf-manager.php", 'laravel-pdf-manager');
        $this->loadViewsFrom("{$this->root}/resources/views", 'laravel-pdf-manager');
        $this->publishConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register facade
        $this->app->singleton('laravel-pdf-manager', function () {
            return new LaravelPdfManagerFacade();
        });
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
