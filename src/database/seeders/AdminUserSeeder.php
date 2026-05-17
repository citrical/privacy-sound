<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::firstOrCreate(
            ['email' => 'admin@privacysound.local'],
            [
                'name' => 'Administrator',
                'email' => 'admin@privacysound.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
