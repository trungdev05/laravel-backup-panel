<?php

namespace Trungdev05\LaravelBackupPanel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Trungdev05\LaravelBackupPanel\Support\BackupPanelConfiguration;

class BackupDisk implements ValidationRule
{
    /**
     * @param  Closure(string, string|null=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! in_array($value, app(BackupPanelConfiguration::class)->disks(), true)) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        return 'Current disk is not configured as a backup disk';
    }
}
