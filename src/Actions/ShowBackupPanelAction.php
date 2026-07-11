<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View as ViewFacade;
use PavelMironchik\LaravelBackupPanel\Http\Requests\ShowBackupPanelRequest;
use PavelMironchik\LaravelBackupPanel\Support\BackupDestinationRepository;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

final readonly class ShowBackupPanelAction
{
    public function __construct(private BackupDestinationRepository $backupDestinationRepository) {}

    public function __invoke(ShowBackupPanelRequest $request): View
    {
        $activeDisk = $request->filled('disk')
            ? $request->string('disk')->value()
            : null;

        $files = $activeDisk === null ? collect() : $this->backupDestinationRepository->backupsForDisk($activeDisk);

        return ViewFacade::make('laravel_backup_panel::index', [
            'activeDisk' => $activeDisk,
            'backupStatuses' => $this->backupStatuses(),
            'files' => $files,
        ]);
    }

    /**
     * @return Collection<int, BackupDestinationStatus>
     */
    private function backupStatuses(): Collection
    {
        return BackupDestinationStatusFactory::createForMonitorConfig(
            app(Config::class)->monitoredBackups,
        );
    }
}
