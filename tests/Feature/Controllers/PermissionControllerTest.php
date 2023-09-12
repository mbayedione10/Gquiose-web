<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Permission;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_permissions(): void
    {
        $permissions = Permission::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('permissions.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.permissions.index')
            ->assertViewHas('permissions');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_permission(): void
    {
        $response = $this->get(route('permissions.create'));

        $response->assertOk()->assertViewIs('app.permissions.create');
    }

    /**
     * @test
     */
    public function it_stores_the_permission(): void
    {
        $data = Permission::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('permissions.store'), $data);

        $this->assertDatabaseHas('permissions', $data);

        $permission = Permission::latest('id')->first();

        $response->assertRedirect(route('permissions.edit', $permission));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->get(route('permissions.show', $permission));

        $response
            ->assertOk()
            ->assertViewIs('app.permissions.show')
            ->assertViewHas('permission');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->get(route('permissions.edit', $permission));

        $response
            ->assertOk()
            ->assertViewIs('app.permissions.edit')
            ->assertViewHas('permission');
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

        $response = $this->put(route('permissions.update', $permission), $data);

        $data['id'] = $permission->id;

        $this->assertDatabaseHas('permissions', $data);

        $response->assertRedirect(route('permissions.edit', $permission));
    }

    /**
     * @test
     */
    public function it_deletes_the_permission(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->delete(route('permissions.destroy', $permission));

        $response->assertRedirect(route('permissions.index'));

        $this->assertModelMissing($permission);
    }
}
