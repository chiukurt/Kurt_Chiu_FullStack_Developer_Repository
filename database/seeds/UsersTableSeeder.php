<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        $admin = User::create([
            'name' => 'Lab Manager [Admin testing account]',
            'username' => 'labmanager',
            'password' => Hash::make('password'),
            'email' => 'lab@manager.com',
            'role' => 'admin'
        ]);
        $user = User::create([
            'name' => 'User [User testing account]',
            'username' => 'user',
            'password' => Hash::make('password'),
            'email' => 'user@user.com',
            'role' => 'user'
        ]);
    }
}
