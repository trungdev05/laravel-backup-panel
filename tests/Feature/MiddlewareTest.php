<?php

namespace PavelMironchik\LaravelBackupPanel\Tests\Feature;

use Illuminate\Routing\Route;
use PavelMironchik\LaravelBackupPanel\Http\Middleware\Authenticate;
use PavelMironchik\LaravelBackupPanel\Tests\TestCase;

class MiddlewareTest extends TestCase
{
    public function test_application_middleware_is_appended_to_mandatory_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('laravel-backup-panel.index');

        self::assertInstanceOf(Route::class, $route);
        self::assertSame([
            'web',
            'auth',
            'can:access-backup-panel',
            Authenticate::class,
        ], $route->middleware());
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        $app['config']->set('laravel_backup_panel.middleware', [
            'auth',
            'can:access-backup-panel',
        ]);
    }
}
