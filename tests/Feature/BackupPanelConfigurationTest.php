<?php

namespace Trungdev05\LaravelBackupPanel\Tests\Feature;

use Illuminate\Support\Facades\Config;
use InvalidArgumentException;
use Trungdev05\LaravelBackupPanel\Support\BackupPanelConfiguration;
use Trungdev05\LaravelBackupPanel\Tests\TestCase;

class BackupPanelConfigurationTest extends TestCase
{
    public function test_monitor_disks_must_exactly_match_backup_destination_disks(): void
    {
        Config::set('backup.monitor_backups', [[
            'name' => 'test-backups',
            'disks' => ['s3'],
            'health_checks' => [],
        ]]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('disks to exactly match backup.backup.destination.disks');

        app(BackupPanelConfiguration::class);
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        Config::set('backup.backup.name', 'test-backups');
        Config::set('backup.backup.destination.disks', ['local']);
        Config::set('backup.monitor_backups', [[
            'name' => 'test-backups',
            'disks' => ['local'],
            'health_checks' => [],
        ]]);
    }
}
