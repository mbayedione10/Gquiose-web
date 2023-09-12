<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Structure;

use App\Models\Ville;
use App\Models\TypeStructure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StructureControllerTest extends TestCase
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
    public function it_displays_index_view_with_structures(): void
    {
        $structures = Structure::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('structures.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.structures.index')
            ->assertViewHas('structures');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_structure(): void
    {
        $response = $this->get(route('structures.create'));

        $response->assertOk()->assertViewIs('app.structures.create');
    }

    /**
     * @test
     */
    public function it_stores_the_structure(): void
    {
        $data = Structure::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('structures.store'), $data);

        $this->assertDatabaseHas('structures', $data);

        $structure = Structure::latest('id')->first();

        $response->assertRedirect(route('structures.edit', $structure));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_structure(): void
    {
        $structure = Structure::factory()->create();

        $response = $this->get(route('structures.show', $structure));

        $response
            ->assertOk()
            ->assertViewIs('app.structures.show')
            ->assertViewHas('structure');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_structure(): void
    {
        $structure = Structure::factory()->create();

        $response = $this->get(route('structures.edit', $structure));

        $response
            ->assertOk()
            ->assertViewIs('app.structures.edit')
            ->assertViewHas('structure');
    }

    /**
     * @test
     */
    public function it_updates_the_structure(): void
    {
        $structure = Structure::factory()->create();

        $typeStructure = TypeStructure::factory()->create();
        $ville = Ville::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(15),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'phone' => $this->faker->unique->phoneNumber(),
            'status' => $this->faker->boolean(),
            'adresse' => $this->faker->text(255),
            'type_structure_id' => $typeStructure->id,
            'ville_id' => $ville->id,
        ];

        $response = $this->put(route('structures.update', $structure), $data);

        $data['id'] = $structure->id;

        $this->assertDatabaseHas('structures', $data);

        $response->assertRedirect(route('structures.edit', $structure));
    }

    /**
     * @test
     */
    public function it_deletes_the_structure(): void
    {
        $structure = Structure::factory()->create();

        $response = $this->delete(route('structures.destroy', $structure));

        $response->assertRedirect(route('structures.index'));

        $this->assertModelMissing($structure);
    }
}
