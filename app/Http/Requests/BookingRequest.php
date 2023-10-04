<?php

namespace App\Http\Requests;

use App\Rules\CannotBookBlockedDate;
use App\Rules\CannotBookWeekends;
use App\Rules\OneBookingPerSlot;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\BetweenNineAMAndFiveThirtyPM;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|numeric',
            'vehicleMake' => 'required|string|max:255',
            'vehicleModel' => 'required|string|max:255',
            'bookingDateTime' => [
                'required',
                'date',
                'after_or_equal:today',
                new BetweenNineAMAndFiveThirtyPM(),
                new OneBookingPerSlot(),
                new CannotBookBlockedDate(),
                new CannotBookWeekends(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.numeric' => 'Phone number must be a number.',
        ];
    }
}
