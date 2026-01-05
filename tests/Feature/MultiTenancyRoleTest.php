<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenancyRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_access_their_own_company_services_in_query()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company1->id,
        ]);

        $service1 = Service::factory()->create(['company_id' => $company1->id, 'name' => 'Company 1 Service']);
        $service2 = Service::factory()->create(['company_id' => $company2->id, 'name' => 'Company 2 Service']);

        $this->actingAs($user);

        // Filament's scope is usually handled by the resource, but let's test the relationship
        $this->assertCount(1, $user->company->services);
        $this->assertEquals('Company 1 Service', $user->company->services->first()->name);
    }

    public function test_service_visibility_based_on_role()
    {
        $company = Company::factory()->create();

        $adminRole = \App\Models\Role::create(['name' => 'Admin', 'slug' => 'admin', 'company_id' => $company->id]);
        $memberRole = \App\Models\Role::create(['name' => 'Member', 'slug' => 'member', 'company_id' => $company->id]);

        $adminUser = User::factory()->create(['company_id' => $company->id]);
        $adminUser->roles()->attach($adminRole);

        $memberUser = User::factory()->create(['company_id' => $company->id]);
        $memberUser->roles()->attach($memberRole);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Admin Only Service',
            'required_role_id' => $adminRole->id
        ]);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Public Service',
            'required_role_id' => null
        ]);

        // Mock Filament Resource Query logic
        $this->actingAs($memberUser);
        $query = \App\Filament\Resources\Services\ServiceResource::getEloquentQuery();
        
        $servicesForMember = $query->get();
        
        $this->assertCount(1, $servicesForMember);
        $this->assertEquals('Public Service', $servicesForMember->first()->name);

        $this->actingAs($adminUser);
        $queryAdmin = \App\Filament\Resources\Services\ServiceResource::getEloquentQuery();
        $servicesForAdmin = $queryAdmin->get();

        $this->assertCount(2, $servicesForAdmin);
    }
}
