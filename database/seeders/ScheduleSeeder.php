<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $labA = Room::where('name', 'Phòng Lab Điện Tử A101')->first();
        $labB = Room::where('name', 'Phòng Lab Mạng B201')->first();

        if ($labA) {
            Schedule::firstOrCreate(
                ['room_id' => $labA->id, 'title' => 'Thực hành Điện tử Cơ bản - Thứ 2'],
                ['recurring_type' => 'weekly', 'day_of_week' => 1, 'start_time' => '07:30', 'end_time' => '11:30']
            );
            Schedule::firstOrCreate(
                ['room_id' => $labA->id, 'title' => 'Thực hành Vi Xử Lý - Thứ 4'],
                ['recurring_type' => 'weekly', 'day_of_week' => 3, 'start_time' => '13:00', 'end_time' => '17:00']
            );
        }

        if ($labB) {
            Schedule::firstOrCreate(
                ['room_id' => $labB->id, 'title' => 'Thực hành Mạng Máy Tính - Thứ 3'],
                ['recurring_type' => 'weekly', 'day_of_week' => 2, 'start_time' => '07:30', 'end_time' => '11:30']
            );
        }
    }
}
