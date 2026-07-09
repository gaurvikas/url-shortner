<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::insert(
            'insert into users (name, email, email_verified_at, password, role, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                'superadmin@sembark.test',
                now(),
                Hash::make('password'),
                'super_admin',
                now(),
                now(),
            ]
        );
    }
}
