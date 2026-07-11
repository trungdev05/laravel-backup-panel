<?php

namespace PavelMironchik\LaravelBackupPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PavelMironchik\LaravelBackupPanel\Console\InstallCommand;
use PavelMironchik\LaravelBackupPanel\Http\Middleware\Authenticate;

class LaravelBackupPanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel_backup_panel.php' => config_path('laravel_backup_panel.php'),
            ], 'laravel-backup-panel-config');

            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel_backup_panel'),
            ], 'laravel-backup-panel-assets');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel_backup_panel'),
            ], 'laravel-backup-panel-views');

            $this->publishes([
                __DIR__.'/../stubs/LaravelBackupPanelServiceProvider.php.stub' => app_path('Providers/LaravelBackupPanelServiceProvider.php'),
            ], 'laravel-backup-panel-provider');

            $this->commands([
                InstallCommand::class,
            ]);
        }

        Route::group([
            'prefix' => Config::string('laravel_backup_panel.path'),
            'middleware' => [
                'web',
                Authenticate::class,
                ...Config::array('laravel_backup_panel.middleware'),
            ],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel_backup_panel');
    }

    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel_backup_panel.php', 'laravel_backup_panel');

        LaravelBackupPanel::auth(static fn (Request $request): bool => false);
    }
}
