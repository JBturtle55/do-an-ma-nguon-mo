<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id'        => Room::factory(),
            'title'          => fake()->optional()->sentence(3),
            'recurring_type' => fake()->randomElement(['weekly', 'daily', 'none']),
            'day_of_week'    => fake()->numberBetween(0, 6),
            'start_time'     => '09:00:00',
            'end_time'       => '11:00:00',
        ];
    }
}
