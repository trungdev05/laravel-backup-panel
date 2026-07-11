<?php

namespace PavelMironchik\LaravelBackupPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PavelMironchik\LaravelBackupPanel\Rules\BackupDisk;

final class ShowBackupPanelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|\Illuminate\Contracts\Validation\ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'disk' => ['nullable', 'string', new BackupDisk()],
        ];
    }
}
