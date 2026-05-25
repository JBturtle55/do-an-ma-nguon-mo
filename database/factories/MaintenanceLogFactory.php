<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceLogFactory extends Factory
{
    public function definition(): array
    {
        $loggable = fake()->boolean(60)
            ? Room::inRandomOrder()->first() ?? Room::factory()->create()
            : Equipment::inRandomOrder()->first() ?? Equipment::factory()->create();

        return [
            'loggable_type' => get_class($loggable),
            'loggable_id'   => $loggable->id,
            'reported_by'   => User::inRandomOrder()->value('id') ?? User::factory(),
            'description'   => fake()->sentence(),
            'status'        => fake()->randomElement(['open', 'in_progress', 'resolved']),
            'resolved_at'   => null,
        ];
    }

    public function forRoom(Room $room): static
    {
        return $this->state([
            'loggable_type' => Room::class,
            'loggable_id'   => $room->id,
        ]);
    }

    public function forEquipment(Equipment $equipment): static
    {
        return $this->state([
            'loggable_type' => Equipment::class,
            'loggable_id'   => $equipment->id,
        ]);
    }
}
