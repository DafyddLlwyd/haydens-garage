<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CannotBookWeekends implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // get the day of the week from the date
        $dayOfWeek = \DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $value)->format('l');

        if ($dayOfWeek === 'Saturday' || $dayOfWeek === 'Sunday') {
            $fail('The garage is closed on weekends. Please choose another date.');
            $fail('The garage is closed on weekends. Please choose another date.');
            return;
        }
    }
}
