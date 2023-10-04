<?php

namespace App\Rules;

use App\Models\Booking;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OneBookingPerSlot implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // use carbon to get the date and time
        $date = Carbon::createFromFormat("Y-m-d\TH:i:s.u\Z", $value)->toDateTimeString();

        // check if there is a booking for this date and time
        $booking = Booking::where('booking_datetime', $value)->first();

        if ($booking) {
            $fail('There is already a booking for this date and time.');
            return;
        }
    }
}
