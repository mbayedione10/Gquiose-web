<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Suivi;
use App\Models\Alerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlerteSuivisTest extends TestCase
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
    public function it_gets_alerte_suivis(): void
    {
        $alerte = Alerte::factory()->create();
        $suivis = Suivi::factory()
            ->count(2)
            ->create([
                'alerte_id' => $alerte->id,
            ]);

        $response = $this->getJson(route('api.alertes.suivis.index', $alerte));

        $response->assertOk()->assertSee($suivis[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_alerte_suivis(): void
    {
        $alerte = Alerte::factory()->create();
        $data = Suivi::factory()
            ->make([
                'alerte_id' => $alerte->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.alertes.suivis.store', $alerte),
            $data
        );

        $this->assertDatabaseHas('suivis', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $suivi = Suivi::latest('id')->first();

        $this->assertEquals($alerte->id, $suivi->alerte_id);
    }
}
