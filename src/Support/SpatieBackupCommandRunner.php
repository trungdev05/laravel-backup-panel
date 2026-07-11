<?php

namespace Trungdev05\LaravelBackupPanel\Support;

use Illuminate\Support\Facades\Artisan;
use Trungdev05\LaravelBackupPanel\Enums\BackupMode;

final class SpatieBackupCommandRunner implements BackupCommandRunner
{
    public function run(BackupMode $mode, BackupFilename $filename): int
    {
        return Artisan::call('backup:run', match ($mode) {
            BackupMode::Full => ['--filename' => $filename->value],
            BackupMode::OnlyDatabase => ['--filename' => $filename->value, '--only-db' => true],
            BackupMode::OnlyFiles => ['--filename' => $filename->value, '--only-files' => true],
        });
    }
}
