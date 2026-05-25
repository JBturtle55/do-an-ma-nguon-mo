<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => 'Phòng ' . strtoupper(fake()->bothify('??###')),
            'building'    => 'Tòa ' . fake()->randomElement(['A', 'B', 'C', 'D']),
            'capacity'    => fake()->randomElement([20, 30, 40, 50, 60]),
            'type'        => fake()->randomElement(['lab', 'classroom', 'workshop']),
            'status'      => 'available',
            'description' => fake()->optional()->sentence(),
        ];
    }

    public function maintenance(): static
    {
        return $this->state(['status' => 'maintenance']);
    }
}
