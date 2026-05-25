<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $rooms    = Room::all();
        $lecturers = User::role('lecturer')->get();

        if ($rooms->isEmpty() || $lecturers->isEmpty()) {
            return;
        }

        foreach ($lecturers->take(3) as $lecturer) {
            $room = $rooms->random();
            Booking::factory()->approved()->forRoom($room)->create([
                'user_id'    => $lecturer->id,
                'title'      => 'Buổi thực hành ' . $room->name,
                'start_time' => now()->addDays(rand(1, 7))->setHour(8)->setMinute(0)->setSecond(0),
                'end_time'   => now()->addDays(rand(1, 7))->setHour(11)->setMinute(0)->setSecond(0),
            ]);

            Booking::factory()->pending()->forRoom($rooms->random())->create([
                'user_id'    => $lecturer->id,
                'title'      => 'Yêu cầu đặt phòng thực hành',
                'start_time' => now()->addDays(rand(8, 14))->setHour(13)->setMinute(0)->setSecond(0),
                'end_time'   => now()->addDays(rand(8, 14))->setHour(16)->setMinute(0)->setSecond(0),
            ]);
        }
    }
}
