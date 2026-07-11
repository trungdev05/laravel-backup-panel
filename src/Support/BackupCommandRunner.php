<?php

namespace PavelMironchik\LaravelBackupPanel\Support;

use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;

interface BackupCommandRunner
{
    public function run(BackupMode $mode, BackupFilename $filename): int;
}
