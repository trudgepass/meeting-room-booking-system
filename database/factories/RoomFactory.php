<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{

    public function definition(): array
    {
        $names = [
            'Conference Room A',
            'Conference Room B',
            'Meeting Room 1',
            'Meeting Room 2',
            'Board Room'
        ];

        return [
            'name' => fake()->unique()->randomElement($names)
        ];
    }
}
