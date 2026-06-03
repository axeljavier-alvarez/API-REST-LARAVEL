<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;
/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'excerpt' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'is_published' => true,
            'published_at' => $this->faker->dateTime,
            // obtiene usuario id random y lo pone alli
            'user_id' => User::all()->random()->id,
            'category_id' => Category::all()->random()->id,

        ];
    }
}
