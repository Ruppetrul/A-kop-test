<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name'     => 'Иван',
            'surname'  => 'Иванов',
            'email'    => 'admin@admin.com',
            'password' => Hash::make('admin'),
        ]);
    }
}
