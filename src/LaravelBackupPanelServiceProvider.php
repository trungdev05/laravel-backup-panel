<?php

namespace PavelMironchik\LaravelBackupPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PavelMironchik\LaravelBackupPanel\Console\InstallCommand;
use PavelMironchik\LaravelBackupPanel\Http\Middleware\Authenticate;
use PavelMironchik\LaravelBackupPanel\Support\BackupCommandRunner;
use PavelMironchik\LaravelBackupPanel\Support\BackupPanelConfiguration;
use PavelMironchik\LaravelBackupPanel\Support\PanelConfiguration;
use PavelMironchik\LaravelBackupPanel\Support\SpatieBackupCommandRunner;
use Spatie\Backup\Config\Config as BackupConfig;

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

        $configuration = $this->app->make(PanelConfiguration::class);

        Route::group([
            'prefix' => $configuration->path(),
            'middleware' => [
                'web',
                ...$configuration->middleware(),
                Authenticate::class,
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

        $this->app->scoped(
            BackupPanelConfiguration::class,
            fn (): BackupPanelConfiguration => new BackupPanelConfiguration($this->app->make(BackupConfig::class)),
        );
        $this->app->scoped(PanelConfiguration::class, PanelConfiguration::class);
        $this->app->bind(BackupCommandRunner::class, SpatieBackupCommandRunner::class);

        LaravelBackupPanel::auth(static fn (Request $request): bool => false);
    }
}
