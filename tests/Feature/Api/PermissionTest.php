<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Permission;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
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
    public function it_gets_permissions_list(): void
    {
        $permissions = Permission::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.permissions.index'));

        $response->assertOk()->assertSee($permissions[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_permission(): void
    {
        $data = Permission::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.permissions.store'), $data);

        $this->assertDatabaseHas('permissions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_permission(): void
    {
        $permission = Permission::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'label' => $this->faker->unique->word(),
            'type' => $this->faker->word(),
        ];

        $response = $this->putJson(
            route('api.permissions.update', $permission),
            $data
        );

        $data['id'] = $permission->id;

        $this->assertDatabaseHas('permissions', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->deleteJson(
            route('api.permissions.destroy', $permission)
        );

        $this->assertModelMissing($permission);

        $response->assertNoContent();
    }
}
