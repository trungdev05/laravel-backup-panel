<?php

namespace Trungdev05\LaravelBackupPanel\Tests;

use Spatie\Backup\BackupServiceProvider;
use Trungdev05\LaravelBackupPanel\LaravelBackupPanelServiceProvider;

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
