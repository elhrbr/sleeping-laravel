<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => (["en" => $this->faker->sentence(), "fr" => $this->faker->sentence(), "ru" => $this->faker->sentence()]),
            'excerpt' => (["en" => $this->faker->paragraph(), "fr" => $this->faker->paragraph(), "ru" => $this->faker->paragraph()]),
            'body' => (["en" => $this->faker->paragraph(), "fr" => $this->faker->paragraph(), "ru" => $this->faker->paragraph()])

        ];
    }
}
