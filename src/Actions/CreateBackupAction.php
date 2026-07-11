<?php

namespace Trungdev05\LaravelBackupPanel\Actions;

use Illuminate\Http\RedirectResponse;
use Trungdev05\LaravelBackupPanel\Enums\BackupMode;
use Trungdev05\LaravelBackupPanel\Http\Requests\CreateBackupRequest;
use Trungdev05\LaravelBackupPanel\Jobs\CreateBackupJob;
use Trungdev05\LaravelBackupPanel\Support\BackupFilename;
use Trungdev05\LaravelBackupPanel\Support\PanelConfiguration;

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
