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
        $this->call([
            RoleSeeder::class,
            CastingItalianSeeder::class,
        ]);

        $company = \App\Models\Company::firstOrCreate(
            ['id' => 1],
            ['name' => 'Hassisto', 'slug' => 'hassisto']
        );

        $superadmin = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@hassisto.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'company_id' => $company->id,
            ]
        );

        $superadmin->roles()->syncWithoutDetaching([
            \App\Models\Role::where('slug', 'superadmin')->first()->id
        ]);
    }
}
