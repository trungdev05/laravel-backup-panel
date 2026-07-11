<?php

namespace PavelMironchik\LaravelBackupPanel\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PathToZip implements ValidationRule
{
    /**
     * @param Closure(string, string|null=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_ends_with($value, '.zip')) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        return 'It must be a zip file';
    }
}
