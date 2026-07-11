<?php

namespace PavelMironchik\LaravelBackupPanel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use Spatie\Backup\Config\Config as BackupConfig;
use Spatie\Backup\Tasks\Backup\BackupJobFactory;

class CreateBackupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public BackupMode $mode)
    {
    }

    public function handle(): void
    {
        $backupJob = BackupJobFactory::createFromConfig(app(BackupConfig::class));

        match ($this->mode) {
            BackupMode::Full => null,
            BackupMode::OnlyDatabase => $backupJob->dontBackupFilesystem(),
            BackupMode::OnlyFiles => $backupJob->dontBackupDatabases(),
        };

        if ($this->mode !== BackupMode::Full) {
            $backupJob->setFilename($this->mode->value.'-'.date('Y-m-d-H-i-s').'.zip');
        }

        $backupJob->run();
    }
}
