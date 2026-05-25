<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+30 days');
        $end   = (clone $start)->modify('+' . fake()->numberBetween(1, 3) . ' hours');

        $bookable = fake()->boolean(70)
            ? Room::inRandomOrder()->first() ?? Room::factory()->create()
            : Equipment::inRandomOrder()->first() ?? Equipment::factory()->create();

        return [
            'user_id'       => User::inRandomOrder()->value('id') ?? User::factory(),
            'bookable_type' => get_class($bookable),
            'bookable_id'   => $bookable->id,
            'title'         => fake()->sentence(3),
            'start_time'    => $start,
            'end_time'      => $end,
            'purpose'       => fake()->optional()->sentence(),
            'status'        => fake()->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function forRoom(Room $room): static
    {
        return $this->state([
            'bookable_type' => Room::class,
            'bookable_id'   => $room->id,
        ]);
    }

    public function forEquipment(Equipment $equipment): static
    {
        return $this->state([
            'bookable_type' => Equipment::class,
            'bookable_id'   => $equipment->id,
        ]);
    }
}
