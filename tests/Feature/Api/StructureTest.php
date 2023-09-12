<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Structure;

use App\Models\Ville;
use App\Models\TypeStructure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StructureTest extends TestCase
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
    public function it_gets_structures_list(): void
    {
        $structures = Structure::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.structures.index'));

        $response->assertOk()->assertSee($structures[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_structure(): void
    {
        $data = Structure::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.structures.store'), $data);

        $this->assertDatabaseHas('structures', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.structures.update', $structure),
            $data
        );

        $data['id'] = $structure->id;

        $this->assertDatabaseHas('structures', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_structure(): void
    {
        $structure = Structure::factory()->create();

        $response = $this->deleteJson(
            route('api.structures.destroy', $structure)
        );

        $this->assertModelMissing($structure);

        $response->assertNoContent();
    }
}
