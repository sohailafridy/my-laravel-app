<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('11223344'),
                'user_type' => '1',
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@gmail.com',
                'password' => Hash::make('11223344'),
                'user_type' => '3',
            ],
            [
                'name' => 'Customer',
                'email' => 'cust@gmail.com',
                'password' => Hash::make('11223344'),
                'user_type' => '2',
            ],
        ];

        foreach ($users as $user) {
            $exists = DB::table('users')
                ->where('email', $user['email'])
                ->exists();

            if ($exists) {
                DB::table('users')
                    ->where('email', $user['email'])
                    ->update([
                        'name' => $user['name'],
                        'password' => $user['password'],
                        'user_type' => $user['user_type'],
                        'updated_at' => $now,
                    ]);

                continue;
            }

            DB::table('users')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => $now,
                'password' => $user['password'],
                'user_type' => $user['user_type'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
