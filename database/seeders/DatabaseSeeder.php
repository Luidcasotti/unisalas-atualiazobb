<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserAdminSeeder::class);

        User::updateOrCreate(
            ['email' => 'professor@unisalas.local'],
            [
                'name' => 'Professor Demo',
                'password' => Hash::make('12345678'),
                'tipo' => 'professor',
                'is_admin' => false,
            ]
        );
    }
}
