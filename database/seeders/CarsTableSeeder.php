<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Car;

class CarsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Car::create([
            'brand' => 'Honda',
            'license_plate' => 'CLMV 191',
            'color' => 'black',
            'street_id' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        Car::create([
            'brand' => 'CRV',
            'license_plate' => 'CLMV 190',
            'color' => 'grey',
            'street_id' => '1',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        Car::create([
            'brand' => 'GMC',
            'license_plate' => 'CLMV 180',
            'color' => 'grey',
            'street_id' => '3',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);


        Car::create([
            'brand' => 'Ford',
            'license_plate' => 'CLMV 192',
            'color' => 'grey',
            'street_id' => '4',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
    }
}
