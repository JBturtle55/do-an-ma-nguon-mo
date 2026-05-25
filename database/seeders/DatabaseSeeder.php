<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            EquipmentCategorySeeder::class,
            RoomSeeder::class,
            EquipmentSeeder::class,
            ScheduleSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
