<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionRolesTest extends TestCase
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
    public function it_gets_permission_roles(): void
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();

        $permission->roles()->attach($role);

        $response = $this->getJson(
            route('api.permissions.roles.index', $permission)
        );

        $response->assertOk()->assertSee($role->name);
    }

    /**
     * @test
     */
    public function it_can_attach_roles_to_permission(): void
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();

        $response = $this->postJson(
            route('api.permissions.roles.store', [$permission, $role])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $permission
                ->roles()
                ->where('roles.id', $role->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_roles_from_permission(): void
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();

        $response = $this->deleteJson(
            route('api.permissions.roles.store', [$permission, $role])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $permission
                ->roles()
                ->where('roles.id', $role->id)
                ->exists()
        );
    }
}
