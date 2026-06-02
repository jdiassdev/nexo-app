<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GodSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'god@nexo.app'],
            [
                'name' => 'God',
                'email' => 'god@nexo.app',
                'password' => Hash::make('1234567'),
                'role' => 'god',
                'school_id' => null,
            ]
        );
    }
}
