<?php

namespace Database\Seeders;

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
        $this->call(UsersTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
        $this->call(StreetsTableSeeder::class);
        $this->call(HousesTableSeeder::class);
        $this->call(CarsTableSeeder::class);
        $this->call(PersonsTableSeeder::class);
        $this->call(OwnersTableSeeder::class);
    }
}
