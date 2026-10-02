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
        // The first admin comes from .env so the password is never committed.
        // Once an admin exists it is left alone: name, email and password are
        // then managed from the admin panel (Account).
        if (! User::exists()) {
            User::create([
                'name' => env('ADMIN_NAME', 'Admin'),
                'email' => env('ADMIN_EMAIL'),
                'password' => env('ADMIN_PASSWORD'),
            ]);
        }

        $this->call(ContentSeeder::class);
    }
}
