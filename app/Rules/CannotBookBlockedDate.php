<?php

namespace App\Rules;

use App\Models\BlockedDate;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CannotBookBlockedDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // use carbon to get the date without time
        $date = \DateTime::createFromFormat("Y-m-d\TH:i:s.u\Z", $value)->format('Y-m-d');

        // check if there is a blocked date for this date
        $blockedDate = BlockedDate::where('locked_date', $date)->first();

        if ($blockedDate) {
            $fail('This date is unavailable and cannot be booked. Please choose another date.');
            return;
        }
    }
}
