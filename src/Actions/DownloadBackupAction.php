<?php

namespace Trungdev05\LaravelBackupPanel\Actions;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Trungdev05\LaravelBackupPanel\Http\Requests\BackupFileRequest;
use Trungdev05\LaravelBackupPanel\Support\BackupDestinationRepository;

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
