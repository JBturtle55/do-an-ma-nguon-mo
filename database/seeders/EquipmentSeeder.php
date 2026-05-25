<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Room;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $electronicsId = EquipmentCategory::where('name', 'Thiết bị điện tử')->value('id');
        $measureId     = EquipmentCategory::where('name', 'Dụng cụ đo lường')->value('id');
        $projectorId   = EquipmentCategory::where('name', 'Thiết bị trình chiếu')->value('id');
        $computerCatId = EquipmentCategory::where('name', 'Máy tính & Phần cứng')->value('id');

        $labA = Room::where('name', 'Phòng Lab Điện Tử A101')->value('id');
        $labB = Room::where('name', 'Phòng Lab Mạng B201')->value('id');

        $items = [
            ['name' => 'Oscilloscope Rigol DS1054Z', 'category_id' => $electronicsId, 'room_id' => $labA, 'quantity' => 5],
            ['name' => 'Multimeter Fluke 117', 'category_id' => $measureId, 'room_id' => $labA, 'quantity' => 10],
            ['name' => 'Máy chiếu Epson EB-X51', 'category_id' => $projectorId, 'room_id' => null, 'quantity' => 3],
            ['name' => 'Raspberry Pi 4 Kit', 'category_id' => $computerCatId, 'room_id' => $labB, 'quantity' => 15],
            ['name' => 'Arduino Uno R3', 'category_id' => $electronicsId, 'room_id' => $labA, 'quantity' => 20],
            ['name' => 'Power Supply Bench 30V', 'category_id' => $electronicsId, 'room_id' => $labA, 'quantity' => 8],
        ];

        foreach ($items as $item) {
            Equipment::firstOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['status' => 'available'])
            );
        }

        Equipment::factory(10)->create();
    }
}
