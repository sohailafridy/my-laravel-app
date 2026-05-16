<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $cities = [
            ['city_name' => 'Kohat', 'country_id' => 1],
            ['city_name' => 'Pindi', 'country_id' => 2],
            ['city_name' => 'Karachi', 'country_id' => 3],
            ['city_name' => 'Gonal', 'country_id' => 4],
        ];

        foreach ($cities as $city) {
            $exists = DB::table('cities')
                ->where('city_name', $city['city_name'])
                ->where('country_id', $city['country_id'])
                ->exists();

            if ($exists) {
                DB::table('cities')
                    ->where('city_name', $city['city_name'])
                    ->where('country_id', $city['country_id'])
                    ->update(['updated_at' => $now]);

                continue;
            }

            DB::table('cities')->insert([
                'city_name' => $city['city_name'],
                'country_id' => $city['country_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
