<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Utilisateur;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilisateurControllerTest extends TestCase
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
    public function it_displays_index_view_with_utilisateurs(): void
    {
        $utilisateurs = Utilisateur::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('utilisateurs.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.utilisateurs.index')
            ->assertViewHas('utilisateurs');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_utilisateur(): void
    {
        $response = $this->get(route('utilisateurs.create'));

        $response->assertOk()->assertViewIs('app.utilisateurs.create');
    }

    /**
     * @test
     */
    public function it_stores_the_utilisateur(): void
    {
        $data = Utilisateur::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('utilisateurs.store'), $data);

        $this->assertDatabaseHas('utilisateurs', $data);

        $utilisateur = Utilisateur::latest('id')->first();

        $response->assertRedirect(route('utilisateurs.edit', $utilisateur));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->get(route('utilisateurs.show', $utilisateur));

        $response
            ->assertOk()
            ->assertViewIs('app.utilisateurs.show')
            ->assertViewHas('utilisateur');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->get(route('utilisateurs.edit', $utilisateur));

        $response
            ->assertOk()
            ->assertViewIs('app.utilisateurs.edit')
            ->assertViewHas('utilisateur');
    }

    /**
     * @test
     */
    public function it_updates_the_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $data = [
            'nom' => $this->faker->text(255),
            'prenom' => $this->faker->text(255),
            'email' => $this->faker->unique->email(),
            'phone' => $this->faker->unique->phoneNumber(),
            'sexe' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->put(
            route('utilisateurs.update', $utilisateur),
            $data
        );

        $data['id'] = $utilisateur->id;

        $this->assertDatabaseHas('utilisateurs', $data);

        $response->assertRedirect(route('utilisateurs.edit', $utilisateur));
    }

    /**
     * @test
     */
    public function it_deletes_the_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->delete(route('utilisateurs.destroy', $utilisateur));

        $response->assertRedirect(route('utilisateurs.index'));

        $this->assertModelMissing($utilisateur);
    }
}
