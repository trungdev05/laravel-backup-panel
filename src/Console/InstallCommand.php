<?php

namespace PavelMironchik\LaravelBackupPanel\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use RuntimeException;

class InstallCommand extends Command
{
    /** @var string */
    protected $signature = 'laravel-backup-panel:install';

    /** @var string */
    protected $description = 'Install all of the Laravel Backup Panel resources';

    public function handle(): int
    {
        $this->comment('Publishing Laravel Backup Panel service provider...');
        $this->publishOrFail('laravel-backup-panel-provider');

        $this->comment('Publishing Laravel Backup Panel assets...');
        $this->publishOrFail('laravel-backup-panel-assets');

        $this->comment('Publishing Laravel Backup Panel views...');
        $this->publishOrFail('laravel-backup-panel-views');

        $this->comment('Publishing Laravel Backup Panel configuration...');
        $this->publishOrFail('laravel-backup-panel-config');

        $this->registerServiceProvider();

        $this->info('Laravel Backup Panel resources installed successfully.');

        return self::SUCCESS;
    }

    private function registerServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());
        $provider = "{$namespace}\\Providers\\LaravelBackupPanelServiceProvider";

        $this->setProviderNamespace($namespace);

        if (! ServiceProvider::addProviderToBootstrapFile($provider)) {
            throw new RuntimeException('Unable to register the Laravel Backup Panel service provider.');
        }
    }

    private function setProviderNamespace(string $namespace): void
    {
        $providerPath = app_path('Providers/LaravelBackupPanelServiceProvider.php');
        $providerStub = File::get($providerPath);
        $namespaceDeclaration = 'namespace App\\Providers;';
        $applicationNamespaceDeclaration = "namespace {$namespace}\\Providers;";

        if (Str::contains($providerStub, $applicationNamespaceDeclaration)) {
            return;
        }

        if (! Str::contains($providerStub, $namespaceDeclaration)) {
            throw new RuntimeException('Published service provider has an unexpected namespace declaration.');
        }

        File::replace(
            $providerPath,
            Str::replace($namespaceDeclaration, $applicationNamespaceDeclaration, $providerStub),
        );
    }

    private function publishOrFail(string $tag): void
    {
        if ($this->callSilent('vendor:publish', ['--tag' => $tag]) !== self::SUCCESS) {
            throw new RuntimeException("Unable to publish resources for [{$tag}].");
        }
    }
}
