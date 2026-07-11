<?php

namespace PavelMironchik\LaravelBackupPanel\Actions;

use PavelMironchik\LaravelBackupPanel\Http\Requests\BackupFileRequest;
use PavelMironchik\LaravelBackupPanel\Support\BackupDestinationRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class DownloadBackupAction
{
    public function __construct(private BackupDestinationRepository $backupDestinationRepository) {}

    public function __invoke(BackupFileRequest $request): StreamedResponse
    {
        $backup = $this->backupDestinationRepository->findOrFail(
            $request->string('disk')->value(),
            $request->string('path')->value(),
        );

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
}
