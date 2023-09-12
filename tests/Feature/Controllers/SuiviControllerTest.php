<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Suivi;

use App\Models\Alerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuiviControllerTest extends TestCase
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
    public function it_displays_index_view_with_suivis(): void
    {
        $suivis = Suivi::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('suivis.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.suivis.index')
            ->assertViewHas('suivis');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_suivi(): void
    {
        $response = $this->get(route('suivis.create'));

        $response->assertOk()->assertViewIs('app.suivis.create');
    }

    /**
     * @test
     */
    public function it_stores_the_suivi(): void
    {
        $data = Suivi::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('suivis.store'), $data);

        $this->assertDatabaseHas('suivis', $data);

        $suivi = Suivi::latest('id')->first();

        $response->assertRedirect(route('suivis.edit', $suivi));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $response = $this->get(route('suivis.show', $suivi));

        $response
            ->assertOk()
            ->assertViewIs('app.suivis.show')
            ->assertViewHas('suivi');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $response = $this->get(route('suivis.edit', $suivi));

        $response
            ->assertOk()
            ->assertViewIs('app.suivis.edit')
            ->assertViewHas('suivi');
    }

    /**
     * @test
     */
    public function it_updates_the_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $alerte = Alerte::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'observation' => $this->faker->sentence(15),
            'alerte_id' => $alerte->id,
        ];

        $response = $this->put(route('suivis.update', $suivi), $data);

        $data['id'] = $suivi->id;

        $this->assertDatabaseHas('suivis', $data);

        $response->assertRedirect(route('suivis.edit', $suivi));
    }

    /**
     * @test
     */
    public function it_deletes_the_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $response = $this->delete(route('suivis.destroy', $suivi));

        $response->assertRedirect(route('suivis.index'));

        $this->assertModelMissing($suivi);
    }
}
