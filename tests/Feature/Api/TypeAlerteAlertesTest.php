<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Alerte;
use App\Models\TypeAlerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeAlerteAlertesTest extends TestCase
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
    public function it_gets_type_alerte_alertes(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();
        $alertes = Alerte::factory()
            ->count(2)
            ->create([
                'type_alerte_id' => $typeAlerte->id,
            ]);

        $response = $this->getJson(
            route('api.type-alertes.alertes.index', $typeAlerte)
        );

        $response->assertOk()->assertSee($alertes[0]->ref);
    }

    /**
     * @test
     */
    public function it_stores_the_type_alerte_alertes(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();
        $data = Alerte::factory()
            ->make([
                'type_alerte_id' => $typeAlerte->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.type-alertes.alertes.store', $typeAlerte),
            $data
        );

        $this->assertDatabaseHas('alertes', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $alerte = Alerte::latest('id')->first();

        $this->assertEquals($typeAlerte->id, $alerte->type_alerte_id);
    }
}
