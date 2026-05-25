<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Thiết bị điện tử',
                'Thiết bị cơ khí',
                'Thiết bị quang học',
                'Máy tính & CNTT',
                'Thiết bị đo lường',
                'Thiết bị y tế',
            ]) . ' ' . fake()->bothify('(##)'),
        ];
    }
}
