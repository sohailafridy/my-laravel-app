<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();

        foreach (['admin', 'customer', 'staff'] as $userType) {
            $exists = DB::table('user_type')
                ->where('user_type', $userType)
                ->exists();

            if ($exists) {
                DB::table('user_type')
                    ->where('user_type', $userType)
                    ->update(['updated_at' => $now]);

                continue;
            }

            DB::table('user_type')->insert([
                'user_type' => $userType,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
