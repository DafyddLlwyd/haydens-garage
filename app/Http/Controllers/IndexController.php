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
    private $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Show the index page.
     */
    public function show(): Response
    {

        return Inertia::render('Index', [
            'bookings' => Booking::all(),
            'blockedDates' => BlockedDate::all(),
        ]);
    }

    /**
     * Store a new booking.
     */
    public function book(BookingRequest $request): \Illuminate\Http\RedirectResponse
    {
         $validated = $request->validated();

         $this->booking->name = $validated['name'];
         $this->booking->email = $validated['email'];
         $this->booking->phone = $validated['phone'];
         $this->booking->vehicle_make = $validated['vehicleMake'];
         $this->booking->vehicle_model = $validated['vehicleModel'];

        // convert bookingDateTime to a DateTime object
        $this->booking->booking_datetime = new \DateTime($validated['bookingDateTime']);
        $this->booking->save();

        $bcc = config('mail.email');

        try {
        Mail::to($validated['email'])
            ->bcc($bcc)
            ->send(new BookingEmail($booking));
        } catch (\Exception $e) {
            // log the error
            \Log::error($e->getMessage());
        }

        return to_route('index');
    }
}
