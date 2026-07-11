<?php

namespace PavelMironchik\LaravelBackupPanel\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use PavelMironchik\LaravelBackupPanel\Rules\BackupDisk;
use PavelMironchik\LaravelBackupPanel\Rules\PathToZip;

final class BackupFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'disk' => ['bail', 'required', 'string', new BackupDisk],
            'path' => ['bail', 'required', 'string', new PathToZip],
        ];
    }
}
