<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ville;
use App\Models\Structure;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VilleStructuresTest extends TestCase
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
    public function it_gets_ville_structures(): void
    {
        $ville = Ville::factory()->create();
        $structures = Structure::factory()
            ->count(2)
            ->create([
                'ville_id' => $ville->id,
            ]);

        $response = $this->getJson(
            route('api.villes.structures.index', $ville)
        );

        $response->assertOk()->assertSee($structures[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_ville_structures(): void
    {
        $ville = Ville::factory()->create();
        $data = Structure::factory()
            ->make([
                'ville_id' => $ville->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.villes.structures.store', $ville),
            $data
        );

        $this->assertDatabaseHas('structures', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $structure = Structure::latest('id')->first();

        $this->assertEquals($ville->id, $structure->ville_id);
    }
}
