<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'customer')->first();

        User::firstOrCreate(
            ['email' => 'customer@bioskop.com'],
            [
                'role_id'  => $role->id,
                'name'     => 'Customer',
                'password' => Hash::make('password'),
                'contact'  => '082222222222',
            ]
        );
    }
}
