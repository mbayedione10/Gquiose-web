<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_role_permissions(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission);

        $response = $this->getJson(route('api.roles.permissions.index', $role));

        $response->assertOk()->assertSee($permission->name);
    }

    /**
     * @test
     */
    public function it_can_attach_permissions_to_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $response = $this->postJson(
            route('api.roles.permissions.store', [$role, $permission])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $role
                ->permissions()
                ->where('permissions.id', $permission->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_permissions_from_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $response = $this->deleteJson(
            route('api.roles.permissions.store', [$role, $permission])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $role
                ->permissions()
                ->where('permissions.id', $permission->id)
                ->exists()
        );
    }
}
