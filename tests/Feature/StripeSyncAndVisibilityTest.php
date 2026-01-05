<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeSyncAndVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_list_view_logic_filters_correctly()
    {
        // Seed roles
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RoleSeeder']);

        $company = Company::factory()->create();
        $adminRole = Role::where('slug', 'admin')->first();
        $memberRole = Role::where('slug', 'member')->first();

        $admin = User::factory()->create(['company_id' => $company->id]);
        $admin->roles()->attach($adminRole);

        $member = User::factory()->create(['company_id' => $company->id]);
        $member->roles()->attach($memberRole);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Admin Service',
            'required_role_id' => $adminRole->id,
            'is_active' => true
        ]);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Member Service',
            'required_role_id' => $memberRole->id,
            'is_active' => true
        ]);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Public Service',
            'required_role_id' => null,
            'is_active' => true
        ]);

        // Testing for member
        $this->actingAs($member);
        \Filament\Facades\Filament::setTenant($company);
        
        $page = new \App\Filament\Pages\PriceList();
        $memberServices = $page->getServices();
        
        $this->assertCount(2, $memberServices);
        $this->assertTrue($memberServices->contains('name', 'Member Service'));
        $this->assertTrue($memberServices->contains('name', 'Public Service'));
        $this->assertFalse($memberServices->contains('name', 'Admin Service'));

        // Testing for admin
        $this->actingAs($admin);
        $adminServices = $page->getServices();
        $this->assertCount(3, $adminServices);
    }
}
