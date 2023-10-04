<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BetweenNineAMAndFiveThirtyPM implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = \DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $value);

        $hour = (int) $date->format('H');
        $minute = (int) $date->format('i');

        if ($hour < 9 || ($hour === 17 && $minute >= 30) || $hour > 17) {
            $fail('The booking date and time must be between 9:00am and 5:30pm.');
            return;
        }
    }
}
