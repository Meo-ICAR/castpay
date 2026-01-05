<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Price;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CastingItalianSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Assicurarsi che i ruoli esistano (Italiano)
        $this->call(RoleSeeder::class);
        
        $adminRole = Role::where('slug', 'admin')->first();
        $actorRole = Role::where('slug', 'actor')->first();
        $workerRole = Role::where('slug', 'worker')->first();
        $bgRole = Role::where('slug', 'backgrounder')->first();

        // 2. Creazione Compagnia di Casting
        $company = Company::firstOrCreate(
            ['slug' => 'cinecitta-casting-pro'],
            ['name' => 'Cinecittà Casting Pro']
        );

        // 3. Utente Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@cinecitta.it'],
            [
                'name' => 'Amministratore Casting',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // 4. Servizi e Listini Prezzi (In Italiano)
        $services = [
            [
                'name' => 'Abbonamento Attori Professionisti',
                'description' => 'Accesso prioritario a casting per ruoli principali e secondari.',
                'required_role_id' => $actorRole->id,
                'prices' => [
                    ['name' => 'Mensile', 'amount' => 2900, 'type' => 'recurring', 'interval' => 'month'],
                    ['name' => 'Annuale', 'amount' => 25000, 'type' => 'recurring', 'interval' => 'year'],
                ]
            ],
            [
                'name' => 'Servizio Comparse e Figurazioni',
                'description' => 'Iscrizione al database per comparse e figurazioni speciali.',
                'required_role_id' => $bgRole->id,
                'prices' => [
                    ['name' => 'Quota Iscrizione Una Tantum', 'amount' => 1500, 'type' => 'one_time'],
                ]
            ],
            [
                'name' => 'Servizio Tecnici e Maestranze',
                'description' => 'Pubblicazione profilo per tecnici, truccatori e parrucchieri.',
                'required_role_id' => $workerRole->id,
                'prices' => [
                    ['name' => 'Mensile Base', 'amount' => 1000, 'type' => 'recurring', 'interval' => 'month'],
                ]
            ],
            [
                'name' => 'Consulenza Immagine e Portfolio',
                'description' => 'Servizio fotografico professionale e creazione book per casting.',
                'required_role_id' => null, // Aperto a tutti
                'prices' => [
                    ['name' => 'Servizio Completo', 'amount' => 15000, 'type' => 'one_time'],
                ]
            ],
        ];

        foreach ($services as $sData) {
            $service = Service::firstOrCreate(
                ['company_id' => $company->id, 'name' => $sData['name']],
                [
                    'description' => $sData['description'],
                    'required_role_id' => $sData['required_role_id'],
                    'is_active' => true,
                ]
            );

            foreach ($sData['prices'] as $pData) {
                Price::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'service_id' => $service->id,
                        'name' => $pData['name'],
                    ],
                    [
                        'amount' => $pData['amount'],
                        'currency' => 'eur',
                        'type' => $pData['type'],
                        'interval' => $pData['interval'] ?? null,
                    ]
                );
            }
        }
    }
}
