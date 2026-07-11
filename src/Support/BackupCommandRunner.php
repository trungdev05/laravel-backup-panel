<?php

namespace Trungdev05\LaravelBackupPanel\Support;

use Trungdev05\LaravelBackupPanel\Enums\BackupMode;

interface BackupCommandRunner
{
    public function run(BackupMode $mode, BackupFilename $filename): int;
}
