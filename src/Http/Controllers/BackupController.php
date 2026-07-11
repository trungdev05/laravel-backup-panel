<?php

namespace PavelMironchik\LaravelBackupPanel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Validation\Rule;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;
use PavelMironchik\LaravelBackupPanel\Jobs\CreateBackupJob;
use PavelMironchik\LaravelBackupPanel\Rules\BackupDisk;
use PavelMironchik\LaravelBackupPanel\Rules\PathToZip;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Config\Config as BackupConfig;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController
{
    public function index(Request $request): View
    {
        Validator::validate($request->all(), [
            'disk' => ['nullable', 'string', new BackupDisk()],
        ]);

        $activeDisk = $request->filled('disk')
            ? $request->string('disk')->value()
            : null;

        $backupStatuses = $this->backupStatuses();
        $files = $activeDisk === null ? collect() : $this->filesForDisk($activeDisk);

        return ViewFacade::make('laravel_backup_panel::index', [
            'activeDisk' => $activeDisk,
            'backupStatuses' => $backupStatuses,
            'files' => $files,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Validator::validate($request->all(), [
            'mode' => ['bail', 'required', 'string', Rule::enum(BackupMode::class)],
        ]);

        $mode = BackupMode::from($request->string('mode')->value());

        CreateBackupJob::dispatch($mode)->onQueue(Config::string('laravel_backup_panel.queue'));

        return to_route('laravel-backup-panel.index')
            ->with('success', 'Creating a new backup in the background.');
    }

    public function download(Request $request): StreamedResponse|Response
    {
        [$disk, $path] = $this->validatedFileInput($request);
        $backup = $this->findBackupOrFail($disk, $path);

        return response()->streamDownload(function () use ($backup): void {
            $stream = $backup->stream();

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, pathinfo($backup->path(), PATHINFO_BASENAME), [
            'Content-Type' => 'application/zip',
            'Content-Length' => (string) $backup->sizeInBytes(),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        [$disk, $path] = $this->validatedFileInput($request);
        $backup = $this->findBackupOrFail($disk, $path);

        $backup->delete();

        throw_if($backup->exists(), \RuntimeException::class, 'Backup deletion failed.');

        return to_route('laravel-backup-panel.index', ['disk' => $disk])
            ->with('success', 'Backup deleted.');
    }

    /**
     * @return Collection<int, BackupDestinationStatus>
     */
    private function backupStatuses(): Collection
    {
        return BackupDestinationStatusFactory::createForMonitorConfig(
            app(BackupConfig::class)->monitoredBackups,
        );
    }

    /**
     * @return Collection<int, Backup>
     */
    private function filesForDisk(string $disk): Collection
    {
        return $this->backupDestination($disk)->backups();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function validatedFileInput(Request $request): array
    {
        Validator::validate($request->all(), [
            'disk' => ['bail', 'required', 'string', new BackupDisk()],
            'path' => ['bail', 'required', 'string', new PathToZip()],
        ]);

        return [$request->string('disk')->value(), $request->string('path')->value()];
    }

    private function backupDestination(string $disk): BackupDestination
    {
        return BackupDestination::create($disk, app(BackupConfig::class)->backup->name);
    }

    private function findBackupOrFail(string $disk, string $path): Backup
    {
        $backup = $this->backupDestination($disk)
            ->backups()
            ->first(fn (Backup $backup): bool => $backup->path() === $path);

        abort_unless($backup instanceof Backup, Response::HTTP_NOT_FOUND, 'Backup not found');

        return $backup;
    }
}
