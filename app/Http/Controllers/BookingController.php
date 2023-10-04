<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockedDateRequest;
use App\Http\Requests\BookingRequest;
use App\Mail\BookingEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Booking;
use App\Models\BlockedDate;

class BookingController extends Controller
{
    public function show(): Response
    {

        return Inertia::render('Bookings', [
            'bookings' => Booking::all(),
            'blockedDates' => BlockedDate::all(),
        ]);
    }

    public function lock(BlockedDateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // save the blocked date
        $blockedDate = new BlockedDate();
        $blockedDate->locked_date = $validated['date'];
        $blockedDate->save();

        return to_route('admin');
    }

    public function unlock(BlockedDateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // delete the blocked date
        BlockedDate::where('locked_date', $validated['date'])->first()->delete();

        return to_route('admin');
    }
}
