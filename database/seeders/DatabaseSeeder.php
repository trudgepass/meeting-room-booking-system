<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $rooms = Room::factory(5)->create();

        // Create 2 bookings for yesterday
        Booking::factory(2)->yesterday()->create([
            'room_id' => fn() => $rooms->random()->id,
        ]);

        // Create 4 bookings for today
        Booking::factory(4)->today()->create([
            'room_id' => fn() => $rooms->random()->id,
        ]);

        // Create 2 bookings for tomorrow
        Booking::factory(2)->tomorrow()->create([
            'room_id' => fn() => $rooms->random()->id,
        ]);
    }
}
