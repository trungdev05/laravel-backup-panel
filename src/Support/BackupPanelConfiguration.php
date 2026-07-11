<?php

namespace PavelMironchik\LaravelBackupPanel\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Spatie\Backup\Config\Config as BackupConfig;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

final readonly class BackupPanelConfiguration
{
    /** @var list<string> */
    private array $disks;

    public function __construct(private BackupConfig $config)
    {
        if ($this->config->backup->name === '') {
            throw new InvalidArgumentException('Backup panel requires a non-empty backup name.');
        }

        $this->disks = $this->validateDisks(
            $this->config->backup->destination->disks,
            'backup.backup.destination.disks',
        );
        $this->validateMonitorContract();
    }

    public function backupName(): string
    {
        return $this->config->backup->name;
    }

    /** @return list<string> */
    public function disks(): array
    {
        return $this->disks;
    }

    public function defaultDisk(): string
    {
        return $this->disks[0];
    }

    /**
     * @return Collection<int, BackupDestinationStatus>
     */
    public function statuses(): Collection
    {
        return BackupDestinationStatusFactory::createForMonitorConfig($this->config->monitoredBackups)
            ->filter(
                fn (BackupDestinationStatus $status): bool => $status->backupDestination()->backupName() === $this->backupName(),
            )
            ->values();
    }

    private function validateMonitorContract(): void
    {
        $matches = 0;

        foreach (Arr::array($this->config->monitoredBackups->toArray(), 'monitorBackups') as $monitor) {
            if (! is_array($monitor)) {
                throw new InvalidArgumentException('Every backup.monitor_backups entry must be an array.');
            }

            $name = Arr::string($monitor, 'name');
            $disks = $this->validateDisks(
                Arr::array($monitor, 'disks'),
                "backup.monitor_backups entry [{$name}].disks",
            );

            if ($name !== $this->config->backup->name) {
                continue;
            }

            if ($disks !== $this->disks) {
                throw new InvalidArgumentException(
                    "Backup panel requires monitor [{$name}] disks to exactly match backup.backup.destination.disks.",
                );
            }

            $matches++;
        }

        if ($matches !== 1) {
            throw new InvalidArgumentException(
                "Backup panel requires exactly one backup.monitor_backups entry named [{$this->config->backup->name}].",
            );
        }
    }

    /**
     * @param  array<array-key, mixed>  $disks
     * @return list<string>
     */
    private function validateDisks(array $disks, string $source): array
    {
        if ($disks === [] || ! array_is_list($disks)) {
            throw new InvalidArgumentException("Configuration [{$source}] must be a non-empty list of disk names.");
        }

        foreach ($disks as $disk) {
            if (! is_string($disk) || $disk === '') {
                throw new InvalidArgumentException("Configuration [{$source}] must contain only non-empty disk names.");
            }
        }

        return $disks;
    }
}
