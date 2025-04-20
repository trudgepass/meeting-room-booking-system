<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('room')->get();

        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        // Check if room is available for the requested time
        $conflictingBooking = Booking::where('room_id', $validated['room_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->whereTime('start_time', '<', $validated['end_time'])
                    ->whereTime('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($conflictingBooking) {
            throw ValidationException::withMessages([
                'room_id' => ['Room already booked for selected time'],
            ]);
        }


        $booking = Booking::create($validated);

        return response()->json($booking->load('room'), 201);
    }
}
