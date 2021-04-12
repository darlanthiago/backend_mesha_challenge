<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'code' => Str::uuid(),
            'name' => "Administrador",
            'email' => 'suporte@sejatech.com',
            'password' => bcrypt('Seja9800@@$$'),
            'profile' => 'admin',
            'is_approved' => true,
            'created_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
