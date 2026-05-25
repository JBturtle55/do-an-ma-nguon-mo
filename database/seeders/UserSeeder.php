<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@lab.test'],
            ['name' => 'Admin Lab', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        $lecturer = User::firstOrCreate(
            ['email' => 'lecturer@lab.test'],
            ['name' => 'Giảng Viên Demo', 'password' => Hash::make('password')]
        );
        $lecturer->syncRoles(['lecturer']);

        $student = User::firstOrCreate(
            ['email' => 'student@lab.test'],
            ['name' => 'Sinh Viên Demo', 'password' => Hash::make('password')]
        );
        $student->syncRoles(['student']);

        User::factory(5)->create()->each(fn ($u) => $u->syncRoles(['lecturer']));
        User::factory(10)->create()->each(fn ($u) => $u->syncRoles(['student']));
    }
}
