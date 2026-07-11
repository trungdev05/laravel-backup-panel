<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use Illuminate\Http\RedirectResponse;
use PavelMironchik\LaravelBackupPanel\Http\Requests\BackupFileRequest;
use PavelMironchik\LaravelBackupPanel\Support\BackupDestinationRepository;

final readonly class DeleteBackupAction
{
    public function __construct(private BackupDestinationRepository $backupDestinationRepository) {}

    public function __invoke(BackupFileRequest $request): RedirectResponse
    {
        $disk = $request->string('disk')->value();
        $backup = $this->backupDestinationRepository->findOrFail(
            $disk,
            $request->string('path')->value(),
        );

        $backup->delete();

        throw_if($backup->exists(), \RuntimeException::class, 'Backup deletion failed.');

        return to_route('laravel-backup-panel.index', ['disk' => $disk])
            ->with('success', __('laravel_backup_panel::panel.backup_deleted'));
    }
}
