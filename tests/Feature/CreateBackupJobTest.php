<?php

namespace Trungdev05\LaravelBackupPanel\Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Trungdev05\LaravelBackupPanel\Enums\BackupMode;
use Trungdev05\LaravelBackupPanel\Jobs\CreateBackupJob;
use Trungdev05\LaravelBackupPanel\Support\BackupCommandRunner;
use Trungdev05\LaravelBackupPanel\Support\BackupFilename;
use Trungdev05\LaravelBackupPanel\Support\SpatieBackupCommandRunner;
use Trungdev05\LaravelBackupPanel\Tests\TestCase;

class CreateBackupJobTest extends TestCase
{
    public function test_file_only_backup_creates_an_archive(): void
    {
        (new CreateBackupJob(BackupMode::OnlyFiles, BackupFilename::create(BackupMode::OnlyFiles)))
            ->handle(new SpatieBackupCommandRunner);

        $files = Storage::disk('local')->files('test-backups');

        self::assertCount(1, $files);
        self::assertIsString($files[0]);
        self::assertMatchesRegularExpression(
            '/^test-backups\/\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2}-only-files-[0-9A-HJKMNP-TV-Z]{26}\.zip$/',
            $files[0],
        );
    }

    public function test_backup_job_runs_the_spatie_command_with_its_mode_and_filename(): void
    {
        $filename = BackupFilename::create(BackupMode::OnlyFiles);

        $runner = new class implements BackupCommandRunner
        {
            public ?BackupMode $mode = null;

            public ?BackupFilename $filename = null;

            public function run(BackupMode $mode, BackupFilename $filename): int
            {
                $this->mode = $mode;
                $this->filename = $filename;

                return Command::SUCCESS;
            }
        };

        (new CreateBackupJob(BackupMode::OnlyFiles, $filename))->handle($runner);

        self::assertSame(BackupMode::OnlyFiles, $runner->mode);
        self::assertSame($filename, $runner->filename);
    }

    public function test_backup_job_marks_the_queue_job_as_failed_when_the_spatie_command_fails(): void
    {
        $filename = BackupFilename::create(BackupMode::OnlyDatabase);

        $runner = new class implements BackupCommandRunner
        {
            public function run(BackupMode $mode, BackupFilename $filename): int
            {
                return Command::FAILURE;
            }
        };

        $job = new CreateBackupJob(BackupMode::OnlyDatabase, $filename);

        $job->withFakeQueueInteractions()->handle($runner);

        $job->assertFailedWith(RuntimeException::class);
    }

    public function test_backup_job_has_one_shared_unique_lock(): void
    {
        $job = new CreateBackupJob(BackupMode::Full, BackupFilename::create(BackupMode::Full));

        self::assertSame('laravel-backup-panel:backup', $job->uniqueId());
        self::assertSame(1, $job->tries);
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
