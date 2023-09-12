<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Thematique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThematiqueControllerTest extends TestCase
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
    public function it_displays_index_view_with_thematiques(): void
    {
        $thematiques = Thematique::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('thematiques.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.thematiques.index')
            ->assertViewHas('thematiques');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_thematique(): void
    {
        $response = $this->get(route('thematiques.create'));

        $response->assertOk()->assertViewIs('app.thematiques.create');
    }

    /**
     * @test
     */
    public function it_stores_the_thematique(): void
    {
        $data = Thematique::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('thematiques.store'), $data);

        $this->assertDatabaseHas('thematiques', $data);

        $thematique = Thematique::latest('id')->first();

        $response->assertRedirect(route('thematiques.edit', $thematique));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $response = $this->get(route('thematiques.show', $thematique));

        $response
            ->assertOk()
            ->assertViewIs('app.thematiques.show')
            ->assertViewHas('thematique');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $response = $this->get(route('thematiques.edit', $thematique));

        $response
            ->assertOk()
            ->assertViewIs('app.thematiques.edit')
            ->assertViewHas('thematique');
    }

    /**
     * @test
     */
    public function it_updates_the_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->put(route('thematiques.update', $thematique), $data);

        $data['id'] = $thematique->id;

        $this->assertDatabaseHas('thematiques', $data);

        $response->assertRedirect(route('thematiques.edit', $thematique));
    }

    /**
     * @test
     */
    public function it_deletes_the_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $response = $this->delete(route('thematiques.destroy', $thematique));

        $response->assertRedirect(route('thematiques.index'));

        $this->assertModelMissing($thematique);
    }
}
