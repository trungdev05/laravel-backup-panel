<?php

namespace PavelMironchik\LaravelBackupPanel\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Jobs\CreateBackupJob;
use PavelMironchik\LaravelBackupPanel\Tests\TestCase;

class CreateBackupJobTest extends TestCase
{
    public function test_file_only_backup_creates_an_archive(): void
    {
        (new CreateBackupJob(BackupMode::OnlyFiles))->handle();

        $files = Storage::disk('local')->files('test-backups');

        self::assertCount(1, $files);
        self::assertIsString($files[0]);
        self::assertMatchesRegularExpression(
            '/^test-backups\/only-files-\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}\.zip$/',
            $files[0],
        );
    }

    protected function getEnvironmentSetUp(mixed $app): void
    {
        $app['config']->set('backup.backup.name', 'test-backups');
        $app['config']->set('backup.backup.source.files.include', [__DIR__.'/../Fixtures']);
        $app['config']->set('backup.backup.source.files.exclude', []);
        $app['config']->set('backup.backup.source.databases', []);
        $app['config']->set('backup.backup.destination.disks', ['local']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }
}
