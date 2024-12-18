<?php

namespace Database\Seeders;

use App\User;
use Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('id', 1)->first();
        if (! isset($user)) {
            User::create([
                'name' => 'Admin',
                'email'=> Config::get('constants.ADMIN_DEFAULT_EMAIL'),
                'username' => 'admin',
                'password' => Hash::make('123admin@'),
                'password_resetted' => 1,
                'role_id' => 1,
            ]);
        }
    }
}
