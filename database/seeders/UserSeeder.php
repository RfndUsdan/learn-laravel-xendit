<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Usdan',
            'email' => 'usdan@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}