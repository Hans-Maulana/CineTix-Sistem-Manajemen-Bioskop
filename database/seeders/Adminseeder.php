<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        User::firstOrCreate(
            ['email' => 'admin@bioskop.com'],
            [
                'role_id'  => $adminRole?->id,
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'contact'  => '081234567890',
            ]
        );
    }
}
