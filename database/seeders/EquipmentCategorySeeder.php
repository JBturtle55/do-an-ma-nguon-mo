<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use Illuminate\Database\Seeder;

class EquipmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Thiết bị điện tử',
            'Dụng cụ đo lường',
            'Thiết bị trình chiếu',
            'Máy tính & Phần cứng',
            'Thiết bị thí nghiệm hóa học',
            'Dụng cụ y tế',
            'Thiết bị âm thanh & hình ảnh',
            'Dụng cụ cơ khí',
        ];

        foreach ($categories as $name) {
            EquipmentCategory::firstOrCreate(['name' => $name]);
        }
    }
}
