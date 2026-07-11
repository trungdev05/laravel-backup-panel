<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use PavelMironchik\LaravelBackupPanel\Http\Requests\ShowBackupPanelRequest;
use PavelMironchik\LaravelBackupPanel\Support\BackupDestinationRepository;
use PavelMironchik\LaravelBackupPanel\Support\BackupPanelConfiguration;

final readonly class ShowBackupPanelAction
{
    public function __construct(
        private BackupDestinationRepository $backupDestinationRepository,
        private BackupPanelConfiguration $configuration,
    ) {}

    public function __invoke(ShowBackupPanelRequest $request): View
    {
        $activeDisk = $request->filled('disk')
            ? $request->string('disk')->value()
            : $this->configuration->defaultDisk();

        return ViewFacade::make('laravel_backup_panel::index', [
            'activeDisk' => $activeDisk,
            'backupStatuses' => $this->configuration->statuses(),
            'files' => $this->backupDestinationRepository->backupsForDisk($activeDisk),
        ]);
    }
}
