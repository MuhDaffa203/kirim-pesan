<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = new User ();
        $user->name = 'Admin';
        $user->email = 'admin@example.com';
        $user->password = bcrypt('12345678');
        $user->role = 'admin';
        $user->save();

        $user = new User ();
        $user->name = 'User';
        $user->email = 'user@example.com';
        $user->password = bcrypt('12345678');
        $user->role = 'user';
        $user->save();
    }
}
