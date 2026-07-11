<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Http\Requests\CreateBackupRequest;
use PavelMironchik\LaravelBackupPanel\Jobs\CreateBackupJob;

final class CreateBackupAction
{
    public function __invoke(CreateBackupRequest $request): RedirectResponse
    {
        CreateBackupJob::dispatch(BackupMode::from($request->string('mode')->value()))
            ->onQueue(Config::string('laravel_backup_panel.queue'));

        return to_route('laravel-backup-panel.index')
            ->with('success', __('laravel_backup_panel::panel.backup_queued'));
    }
}
