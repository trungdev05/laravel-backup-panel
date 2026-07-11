<?php

namespace PavelMironchik\LaravelBackupPanel\Tests;

use PavelMironchik\LaravelBackupPanel\LaravelBackupPanelServiceProvider;
use Spatie\Backup\BackupServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders(mixed $app): array
    {
        return [
            LaravelBackupPanelServiceProvider::class,
            BackupServiceProvider::class,
        ];
    }
}
