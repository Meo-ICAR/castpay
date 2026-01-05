<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin',
            'Manager',
            'Member',
            'Actor',
            'Worker',
            'Rentaler',
            'Backgrounder',
            'Locarioner',
            'Coordinator'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['slug' => Str::slug($roleName)],
                ['name' => $roleName]
            );
        }
    }
}
