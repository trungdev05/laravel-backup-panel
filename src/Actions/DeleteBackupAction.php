<?php

namespace Trungdev05\LaravelBackupPanel\Actions;

use Illuminate\Http\RedirectResponse;
use Trungdev05\LaravelBackupPanel\Http\Requests\BackupFileRequest;
use Trungdev05\LaravelBackupPanel\Support\BackupDestinationRepository;

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
            ->with('success', 'Backup deleted.');
    }
}
