<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\TypeStructure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeStructureControllerTest extends TestCase
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
    public function it_displays_index_view_with_type_structures(): void
    {
        $typeStructures = TypeStructure::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('type-structures.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.type_structures.index')
            ->assertViewHas('typeStructures');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_type_structure(): void
    {
        $response = $this->get(route('type-structures.create'));

        $response->assertOk()->assertViewIs('app.type_structures.create');
    }

    /**
     * @test
     */
    public function it_stores_the_type_structure(): void
    {
        $data = TypeStructure::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('type-structures.store'), $data);

        $this->assertDatabaseHas('type_structures', $data);

        $typeStructure = TypeStructure::latest('id')->first();

        $response->assertRedirect(
            route('type-structures.edit', $typeStructure)
        );
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_type_structure(): void
    {
        $typeStructure = TypeStructure::factory()->create();

        $response = $this->get(route('type-structures.show', $typeStructure));

        $response
            ->assertOk()
            ->assertViewIs('app.type_structures.show')
            ->assertViewHas('typeStructure');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_type_structure(): void
    {
        $typeStructure = TypeStructure::factory()->create();

        $response = $this->get(route('type-structures.edit', $typeStructure));

        $response
            ->assertOk()
            ->assertViewIs('app.type_structures.edit')
            ->assertViewHas('typeStructure');
    }

    /**
     * @test
     */
    public function it_updates_the_type_structure(): void
    {
        $typeStructure = TypeStructure::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'icon' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->put(
            route('type-structures.update', $typeStructure),
            $data
        );

        $data['id'] = $typeStructure->id;

        $this->assertDatabaseHas('type_structures', $data);

        $response->assertRedirect(
            route('type-structures.edit', $typeStructure)
        );
    }

    /**
     * @test
     */
    public function it_deletes_the_type_structure(): void
    {
        $typeStructure = TypeStructure::factory()->create();

        $response = $this->delete(
            route('type-structures.destroy', $typeStructure)
        );

        $response->assertRedirect(route('type-structures.index'));

        $this->assertModelMissing($typeStructure);
    }
}
