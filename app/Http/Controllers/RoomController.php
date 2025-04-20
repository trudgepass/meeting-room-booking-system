<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailableRoomsRequest;
use App\Models\Booking;
use App\Models\Room;

class RoomController extends Controller
{
    public function index(AvailableRoomsRequest $request)
    {
        $validated = $request->validated();

        $bookedRooms = Booking::where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })
            ->pluck('room_id');

        $availableRooms = Room::whereNotIn('id', $bookedRooms)->get();

        return response()->json($availableRooms);
    }
}
