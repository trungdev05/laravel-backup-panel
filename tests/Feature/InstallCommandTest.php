<?php

namespace Trungdev05\LaravelBackupPanel\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Trungdev05\LaravelBackupPanel\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    public function test_install_command_publishes_assets(): void
    {
        $directory = public_path('vendor/laravel_backup_panel');

        self::assertTrue(File::exists($directory.'/css/app.css'));
        self::assertTrue(File::exists($directory.'/css/bootstrap.min.css'));
        self::assertTrue(File::exists($directory.'/js/app.js'));
        self::assertTrue(File::exists($directory.'/js/bootstrap.bundle.min.js'));
    }

    public function test_install_command_publishes_views(): void
    {
        $directory = resource_path('views/vendor/laravel_backup_panel');

        self::assertTrue(File::exists($directory.'/index.blade.php'));
        self::assertTrue(File::exists($directory.'/layout.blade.php'));
    }

    public function test_install_command_publishes_config(): void
    {
        self::assertTrue(File::exists(config_path('laravel_backup_panel.php')));
    }

    public function test_install_command_publishes_provider(): void
    {
        self::assertTrue(File::exists(app_path('Providers/LaravelBackupPanelServiceProvider.php')));
    }

    public function test_install_command_sets_namespace_for_provider(): void
    {
        $namespace = Str::replaceLast('\\', '', app()->getNamespace());
        $provider = File::get(app_path('Providers/LaravelBackupPanelServiceProvider.php'));

        self::assertTrue(Str::contains($provider, "namespace {$namespace}\\Providers;"));
    }

    public function test_install_command_registers_provider(): void
    {
        $namespace = Str::replaceLast('\\', '', app()->getNamespace());
        $providers = File::get(base_path('bootstrap/providers.php'));

        self::assertTrue(Str::contains($providers, "{$namespace}\\Providers\\LaravelBackupPanelServiceProvider::class"));
    }

    public function test_install_command_registers_the_provider_once(): void
    {
        Artisan::call('laravel-backup-panel:install');

        $namespace = Str::replaceLast('\\', '', app()->getNamespace());
        $providers = File::get(base_path('bootstrap/providers.php'));

        self::assertSame(1, substr_count($providers, "{$namespace}\\Providers\\LaravelBackupPanelServiceProvider::class"));
    }

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('laravel-backup-panel:install');
    }

    protected function tearDown(): void
    {
        $this->clearFiles();

        parent::tearDown();
    }

    private function clearFiles(): void
    {
        foreach ([
            public_path('vendor/laravel_backup_panel'),
            resource_path('views/vendor/laravel_backup_panel'),
        ] as $path) {
            if (File::exists($path)) {
                File::deleteDirectory($path);
            }
        }

        foreach ([
            config_path('laravel_backup_panel.php'),
            app_path('Providers/LaravelBackupPanelServiceProvider.php'),
        ] as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $namespace = Str::replaceLast('\\', '', app()->getNamespace());
        $providersPath = base_path('bootstrap/providers.php');
        $providers = File::get($providersPath);

        File::replace($providersPath, str_replace(
            "    {$namespace}\\Providers\\LaravelBackupPanelServiceProvider::class,".PHP_EOL,
            '',
            $providers
        ));
    }
}
