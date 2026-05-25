<?php

namespace Database\Factories;

use App\Models\EquipmentCategory;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => fake()->randomElement(['Máy chiếu', 'Kính hiển vi', 'Oscilloscope', 'Multimeter', 'Máy tính', 'Camera', 'Loa', 'Bảng thông minh']) . ' ' . fake()->bothify('##'),
            'category_id' => EquipmentCategory::inRandomOrder()->value('id') ?? EquipmentCategory::factory(),
            'room_id'     => fake()->boolean(70) ? Room::inRandomOrder()->value('id') : null,
            'quantity'    => fake()->numberBetween(1, 10),
            'status'      => 'available',
            'description' => fake()->optional()->sentence(),
        ];
    }
}
