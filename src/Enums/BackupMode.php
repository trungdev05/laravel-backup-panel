<?php

namespace PavelMironchik\LaravelBackupPanel\Enums;

enum BackupMode: string
{
    case Full = 'full';
    case OnlyDatabase = 'only-db';
    case OnlyFiles = 'only-files';
}
