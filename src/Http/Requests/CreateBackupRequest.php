<?php

namespace PavelMironchik\LaravelBackupPanel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use PavelMironchik\LaravelBackupPanel\Enums\BackupMode;

final class CreateBackupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|\Illuminate\Contracts\Validation\Rule>>
     */
    public function rules(): array
    {
        return [
            'mode' => ['bail', 'required', 'string', Rule::enum(BackupMode::class)],
        ];
    }
}
