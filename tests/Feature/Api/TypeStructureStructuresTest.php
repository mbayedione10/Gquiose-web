<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Structure;
use App\Models\TypeStructure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeStructureStructuresTest extends TestCase
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
    public function it_gets_type_structure_structures(): void
    {
        $typeStructure = TypeStructure::factory()->create();
        $structures = Structure::factory()
            ->count(2)
            ->create([
                'type_structure_id' => $typeStructure->id,
            ]);

        $response = $this->getJson(
            route('api.type-structures.structures.index', $typeStructure)
        );

        $response->assertOk()->assertSee($structures[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_type_structure_structures(): void
    {
        $typeStructure = TypeStructure::factory()->create();
        $data = Structure::factory()
            ->make([
                'type_structure_id' => $typeStructure->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.type-structures.structures.store', $typeStructure),
            $data
        );

        $this->assertDatabaseHas('structures', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $structure = Structure::latest('id')->first();

        $this->assertEquals($typeStructure->id, $structure->type_structure_id);
    }
}
