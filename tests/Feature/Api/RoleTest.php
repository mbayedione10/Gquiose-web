<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
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
    public function it_gets_roles_list(): void
    {
        $roles = Role::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.roles.index'));

        $response->assertOk()->assertSee($roles[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_role(): void
    {
        $data = Role::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.roles.store'), $data);

        $this->assertDatabaseHas('roles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_role(): void
    {
        $role = Role::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->putJson(route('api.roles.update', $role), $data);

        $data['id'] = $role->id;

        $this->assertDatabaseHas('roles', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson(route('api.roles.destroy', $role));

        $this->assertModelMissing($role);

        $response->assertNoContent();
    }
}
