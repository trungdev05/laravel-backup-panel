<?php

namespace PavelMironchik\LaravelBackupPanel\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Backup\Config\Config as BackupConfig;

class BackupDisk implements Rule
{
    public function passes($attribute, $value): bool
    {
        return in_array($value, app(BackupConfig::class)->backup->destination->disks, true);
    }

    public function message(): string
    {
        return 'Current disk is not configured as a backup disk';
    }
}
