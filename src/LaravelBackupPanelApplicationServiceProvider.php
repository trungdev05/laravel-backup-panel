<?php

namespace PavelMironchik\LaravelBackupPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LaravelBackupPanelApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureAuthorization();
    }

    /**
     * Configure the Laravel Backup Panel authorization services.
     */
    protected function configureAuthorization(): void
    {
        $this->gate();

        LaravelBackupPanel::auth(function (Request $request): bool {
            if (App::environment('local')) {
                return true;
            }

            return Gate::check('viewLaravelBackupPanel', [$request->user()]);
        });
    }

    /**
     * Register the Laravel Backup Panel gate.
     *
     * This gate determines who can access Laravel Backup Panel in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewLaravelBackupPanel', static fn (): bool => false);
    }

    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }
}
