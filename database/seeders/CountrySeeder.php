<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $countries = ['KPK', 'Punjab', 'Sindh', 'Balochistan'];

        foreach ($countries as $country) {
            $exists = DB::table('countries')
                ->where('country_name', $country)
                ->exists();

            if ($exists) {
                DB::table('countries')
                    ->where('country_name', $country)
                    ->update(['updated_at' => $now]);

                continue;
            }

            DB::table('countries')->insert([
                'country_name' => $country,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
