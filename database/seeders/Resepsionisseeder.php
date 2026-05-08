<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class Resepsionisseeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'resepsionis')->first();

        User::firstOrCreate(
            ['email' => 'resepsionis@bioskop.com'],
            [
                'role_id'  => $role->id,
                'name'     => 'Resepsionis',
                'password' => Hash::make('password'),
                'contact'  => '081111111111',
            ]
        );
    }
}
