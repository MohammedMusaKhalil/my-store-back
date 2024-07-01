<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'mm@gmail.com',
            'address' => 'izaz',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);
    }
}
