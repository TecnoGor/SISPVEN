<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CodigosTelefono implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Nacional: 0212-0299 (fijos), 0412, 0414, 0416, 0424, 0426 (móviles)
        $esVenezolano = preg_match('/^(02\d{2}|04(12|14|16|22|24|26))\d{7}$/', $value);

        // Internacional: + seguido de 8 a 15 dígitos
        $esInternacional = preg_match('/^\+\d{8,15}$/', $value);

        if (!$esVenezolano && !$esInternacional) {
            $fail("El campo {$attribute} debe ser un número telefónico válido (nacional o internacional).");
        }
    }
}
