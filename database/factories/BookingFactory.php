<?php

namespace Database\Factories;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    private static $timeSlots = [
        ['09:00', '10:00'],
        ['11:30', '13:30'],
        ['14:00', '16:00'],
        ['16:30', '18:00'],
    ];

    public function definition(): array
    {
        // Default to today
        $date = Carbon::today();

        // Get a random time slot (these don't overlap)
        $timeSlotIndex = fake()->numberBetween(0, count(self::$timeSlots) - 1);
        $timeSlot = self::$timeSlots[$timeSlotIndex];

        return [
            'room_id' => Room::factory(),
            'user_name' => fake()->userName(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $timeSlot[0],
            'end_time' => $timeSlot[1],
        ];
    }

    /**
     * Configure booking for yesterday
     */
    public function yesterday(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => Carbon::yesterday()->format('Y-m-d'),
        ]);
    }

    /**
     * Configure booking for today
     */
    public function today(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => Carbon::today()->format('Y-m-d'),
        ]);
    }

    /**
     * Configure booking for tomorrow
     */
    public function tomorrow(): static
    {
        return $this->state(fn(array $attributes) => [
            'date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);
    }
}
