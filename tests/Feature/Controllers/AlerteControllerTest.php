<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Alerte;

use App\Models\Ville;
use App\Models\TypeAlerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlerteControllerTest extends TestCase
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
    public function it_displays_index_view_with_alertes(): void
    {
        $alertes = Alerte::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('alertes.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.alertes.index')
            ->assertViewHas('alertes');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_alerte(): void
    {
        $response = $this->get(route('alertes.create'));

        $response->assertOk()->assertViewIs('app.alertes.create');
    }

    /**
     * @test
     */
    public function it_stores_the_alerte(): void
    {
        $data = Alerte::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('alertes.store'), $data);

        $this->assertDatabaseHas('alertes', $data);

        $alerte = Alerte::latest('id')->first();

        $response->assertRedirect(route('alertes.edit', $alerte));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $response = $this->get(route('alertes.show', $alerte));

        $response
            ->assertOk()
            ->assertViewIs('app.alertes.show')
            ->assertViewHas('alerte');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $response = $this->get(route('alertes.edit', $alerte));

        $response
            ->assertOk()
            ->assertViewIs('app.alertes.edit')
            ->assertViewHas('alerte');
    }

    /**
     * @test
     */
    public function it_updates_the_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $typeAlerte = TypeAlerte::factory()->create();
        $ville = Ville::factory()->create();

        $data = [
            'ref' => $this->faker->unique->text(255),
            'description' => $this->faker->sentence(15),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'etat' => $this->faker->word(),
            'type_alerte_id' => $typeAlerte->id,
            'ville_id' => $ville->id,
        ];

        $response = $this->put(route('alertes.update', $alerte), $data);

        $data['id'] = $alerte->id;

        $this->assertDatabaseHas('alertes', $data);

        $response->assertRedirect(route('alertes.edit', $alerte));
    }

    /**
     * @test
     */
    public function it_deletes_the_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $response = $this->delete(route('alertes.destroy', $alerte));

        $response->assertRedirect(route('alertes.index'));

        $this->assertModelMissing($alerte);
    }
}
