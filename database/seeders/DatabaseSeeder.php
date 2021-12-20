<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(PaymentPlatformsSeeder::class);
        $this->call(CurrenciesSeeder::class);
        $this->call(UserSeeder::class);
    }
}
