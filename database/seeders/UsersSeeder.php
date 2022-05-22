<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $users = [
            [
                'name' => 'admin',
                'first_name' => 'shakhawat',
                'last_name' => 'hossain',
                'email' => 'shakhawat@email.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Moris',
                'first_name' => 'Moris',
                'last_name' => 'Super admin',
                'email' => 'moris@email.com',
                'password' => Hash::make('password'),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
