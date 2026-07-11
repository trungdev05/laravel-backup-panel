<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use Illuminate\Http\RedirectResponse;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Http\Requests\CreateBackupRequest;
use PavelMironchik\LaravelBackupPanel\Jobs\CreateBackupJob;
use PavelMironchik\LaravelBackupPanel\Support\BackupFilename;
use PavelMironchik\LaravelBackupPanel\Support\PanelConfiguration;

final readonly class CreateBackupAction
{
    public function __construct(private PanelConfiguration $configuration) {}

    public function __invoke(CreateBackupRequest $request): RedirectResponse
    {
        $mode = BackupMode::from($request->string('mode')->value());

        CreateBackupJob::dispatch($mode, BackupFilename::create($mode))
            ->onQueue($this->configuration->queue());

        return to_route('laravel-backup-panel.index')
            ->with('success', 'Backup request is queued or already running.');
    }
}
