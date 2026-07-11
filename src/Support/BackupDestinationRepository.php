<?php

namespace PavelMironchik\LaravelBackupPanel\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Config\Config as BackupConfig;

final readonly class BackupDestinationRepository
{
    public function __construct(private BackupConfig $config) {}

    /**
     * @return Collection<int, Backup>
     */
    public function backupsForDisk(string $disk): Collection
    {
        return $this->backupDestination($disk)->backups();
    }

    public function findOrFail(string $disk, string $path): Backup
    {
        $backup = $this->backupsForDisk($disk)
            ->first(fn (Backup $backup): bool => $backup->path() === $path);

        abort_unless($backup instanceof Backup, Response::HTTP_NOT_FOUND, 'Backup not found');

        return $backup;
    }

    private function backupDestination(string $disk): BackupDestination
    {
        return BackupDestination::create($disk, $this->config->backup->name);
    }
}
