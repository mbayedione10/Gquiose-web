<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\TypeStructure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeStructureTest extends TestCase
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
    public function it_gets_type_structures_list(): void
    {
        $typeStructures = TypeStructure::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.type-structures.index'));

        $response->assertOk()->assertSee($typeStructures[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_type_structure(): void
    {
        $data = TypeStructure::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.type-structures.store'), $data);

        $this->assertDatabaseHas('type_structures', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.type-structures.update', $typeStructure),
            $data
        );

        $data['id'] = $typeStructure->id;

        $this->assertDatabaseHas('type_structures', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_type_structure(): void
    {
        $typeStructure = TypeStructure::factory()->create();

        $response = $this->deleteJson(
            route('api.type-structures.destroy', $typeStructure)
        );

        $this->assertModelMissing($typeStructure);

        $response->assertNoContent();
    }
}
