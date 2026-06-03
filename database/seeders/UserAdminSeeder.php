<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'l@gmail.com'],
            [
                'name' => 'Administrador UniSalas',
                'password' => Hash::make('12345678'),
                'tipo' => 'admin',
                'is_admin' => true,
            ]
        );
    }
}
