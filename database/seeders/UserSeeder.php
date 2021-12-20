<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Abraham',
            'email' => 'abrahamag93@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('abraham')
        ]);
    }
}

