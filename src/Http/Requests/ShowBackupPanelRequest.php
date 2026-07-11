<?php

namespace Trungdev05\LaravelBackupPanel\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Trungdev05\LaravelBackupPanel\Rules\BackupDisk;

final class ShowBackupPanelRequest extends FormRequest
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
            'disk' => ['nullable', 'string', new BackupDisk],
        ];
    }
}
