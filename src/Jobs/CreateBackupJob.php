<?php

namespace PavelMironchik\LaravelBackupPanel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Support\BackupCommandRunner;
use PavelMironchik\LaravelBackupPanel\Support\BackupFilename;
use RuntimeException;

class CreateBackupJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public BackupMode $mode,
        public BackupFilename $filename,
    ) {}

    public function uniqueId(): string
    {
        return 'laravel-backup-panel:backup';
    }

    public function handle(BackupCommandRunner $backupCommandRunner): void
    {
        $exitCode = $backupCommandRunner->run($this->mode, $this->filename);

        if ($exitCode !== Command::SUCCESS) {
            $this->fail(new RuntimeException('Spatie backup command failed.'));
        }
    }
}
