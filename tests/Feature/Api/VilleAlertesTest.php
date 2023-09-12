<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Ville;
use App\Models\Alerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VilleAlertesTest extends TestCase
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
    public function it_gets_ville_alertes(): void
    {
        $ville = Ville::factory()->create();
        $alertes = Alerte::factory()
            ->count(2)
            ->create([
                'ville_id' => $ville->id,
            ]);

        $response = $this->getJson(route('api.villes.alertes.index', $ville));

        $response->assertOk()->assertSee($alertes[0]->ref);
    }

    /**
     * @test
     */
    public function it_stores_the_ville_alertes(): void
    {
        $ville = Ville::factory()->create();
        $data = Alerte::factory()
            ->make([
                'ville_id' => $ville->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.villes.alertes.store', $ville),
            $data
        );

        $this->assertDatabaseHas('alertes', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $alerte = Alerte::latest('id')->first();

        $this->assertEquals($ville->id, $alerte->ville_id);
    }
}
