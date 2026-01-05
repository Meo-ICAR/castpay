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
            'role' => 'admin',
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

        $adminUser = User::factory()->create(['company_id' => $company->id, 'role' => 'admin']);
        $memberUser = User::factory()->create(['company_id' => $company->id, 'role' => 'member']);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Admin Only Service',
            'required_role' => 'admin'
        ]);

        Service::factory()->create([
            'company_id' => $company->id,
            'name' => 'Public Service',
            'required_role' => null
        ]);

        // Mock Filament Resource Query logic
        $this->actingAs($memberUser);
        $query = \App\Filament\Resources\Services\ServiceResource::getEloquentQuery();
        
        // Use whereHas or scope to ensure we are looking at the right company if needed, 
        // but here we just check the global query filter we implemented
        $servicesForMember = $query->get();
        
        $this->assertCount(1, $servicesForMember);
        $this->assertEquals('Public Service', $servicesForMember->first()->name);

        $this->actingAs($adminUser);
        $queryAdmin = \App\Filament\Resources\Services\ServiceResource::getEloquentQuery();
        $servicesForAdmin = $queryAdmin->get();

        $this->assertCount(2, $servicesForAdmin);
    }
}
