<?php

namespace Database\Seeders;

use App\Model\FrontendUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FrontendUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FrontendUser::truncate();
        FrontendUser::create([
            'name' => 'binaya',
            'username' => 'binaya',
            'email' => 'binaya+2@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
