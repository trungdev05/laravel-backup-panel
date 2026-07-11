<?php

namespace Trungdev05\LaravelBackupPanel\Support;

use Illuminate\Support\Str;
use Trungdev05\LaravelBackupPanel\Enums\BackupMode;

final readonly class BackupFilename
{
    private function __construct(public string $value) {}

    public static function create(BackupMode $mode): self
    {
        return new self(
            now()->format('Y-m-d-H-i-s').'-'.$mode->value.'-'.Str::ulid().'.zip',
        );
    }
}
