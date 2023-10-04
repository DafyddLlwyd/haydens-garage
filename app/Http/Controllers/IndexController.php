<?php
namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\BlockedDate;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingEmail;

class IndexController extends Controller
{
    public function show(): Response
    {

        return Inertia::render('Index', [
            'bookings' => Booking::all(),
            'blockedDates' => BlockedDate::all(),
        ]);
    }

    public function book(BookingRequest $request): \Illuminate\Http\RedirectResponse
    {
         $validated = $request->validated();

         $booking = new Booking();
         $booking->name = $validated['name'];
         $booking->email = $validated['email'];
         $booking->phone = $validated['phone'];
         $booking->vehicle_make = $validated['vehicleMake'];
         $booking->vehicle_model = $validated['vehicleModel'];

        // convert bookingDateTime to a DateTime object
        $booking->booking_datetime = new \DateTime($validated['bookingDateTime']);
        $booking->save();

        $bcc = config('mail.email');

        Mail::to($validated['email'])
            ->bcc($bcc)
            ->send(new BookingEmail($booking));

        return to_route('index');
    }
}
