<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Person;

class PersonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    Person::create([
            'name' => 'John Deo',
            'age' => '20',
            'city_id' => '1',
            'house_id' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);

        Person::create([
            'name' => 'Alison',
            'age' => '22',
            'city_id' => '1',
            'house_id' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);

        Person::create([
            'name' => 'Amy',
            'age' => '26',
            'city_id' => '1',
            'house_id' => '2',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);


        Person::create([
            'name' => 'Alison',
            'age' => '24',
            'city_id' => '1',
            'house_id' => '3',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);

        Person::create([
            'name' => 'Audrey',
            'age' => '25',
            'city_id' => '1',
            'house_id' => '4',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
	    ]);
    	
    }
}
