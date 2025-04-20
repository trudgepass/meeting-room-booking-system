<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $bookings = Booking::with('room')
        ->where(function ($query) {
            $today = now()->format('Y-m-d');
            $query->where('date', '>', $today)
                ->orWhere(function ($query) use ($today) {
                    $query->where('date', '=', $today)
                        ->where('end_time', '>', now()->format('H:i:s'));
                });
        })
        ->orderBy('date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();

    return Inertia::render('Dashboard', [
        'bookings' => $bookings,
    ]);
})->name('dashboard');


// require __DIR__ . '/settings.php';
// require __DIR__ . '/auth.php';
