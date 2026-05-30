<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StandardUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::firstOrCreate(
            ['email' => 'flimleo420@g.educaand.es'],
            [
                'name' => 'Francisco Limón León',
                'email' => 'flimleo420@g.educaand.es',
                'password' => Hash::make('TXYRX@2QR9gg'),
                'role' => 'user',
            ]
        );
    }
}