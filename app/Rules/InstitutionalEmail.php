<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InstitutionalEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || strtolower(substr(strrchr($value, '@') ?: '', 1)) !== strtolower(config('institution.email_domain'))) {
            $fail('Debes utilizar tu correo institucional.');
        }
    }
}
