<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(10),
            'description' => $this->faker->sentence(15),
            'slug' => $this->faker->unique->slug(),
            'status' => $this->faker->boolean(),
            'video_url' => $this->faker->text(255),
            'audio_url' => $this->faker->text(255),
            'rubrique_id' => \App\Models\Rubrique::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
