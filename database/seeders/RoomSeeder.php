<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'Phòng Lab Điện Tử A101', 'building' => 'Tòa A', 'capacity' => 30, 'type' => 'lab'],
            ['name' => 'Phòng Lab Mạng B201', 'building' => 'Tòa B', 'capacity' => 25, 'type' => 'lab'],
            ['name' => 'Phòng Lab Hóa C301', 'building' => 'Tòa C', 'capacity' => 20, 'type' => 'lab'],
            ['name' => 'Phòng Thực Hành Cơ Khí D101', 'building' => 'Tòa D', 'capacity' => 40, 'type' => 'workshop'],
            ['name' => 'Phòng Học A102', 'building' => 'Tòa A', 'capacity' => 50, 'type' => 'classroom'],
            ['name' => 'Phòng Học B202', 'building' => 'Tòa B', 'capacity' => 45, 'type' => 'classroom'],
            ['name' => 'Phòng Lab AI E501', 'building' => 'Tòa E', 'capacity' => 30, 'type' => 'lab'],
            ['name' => 'Xưởng Thực Hành D201', 'building' => 'Tòa D', 'capacity' => 35, 'type' => 'workshop'],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(['name' => $room['name']], array_merge($room, ['status' => 'available']));
        }

        Room::factory(5)->create();
    }
}
