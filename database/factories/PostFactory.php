<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(rand(3, 6), true));

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(14),
            'content' => fake()->paragraphs(4, true),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'published_at' => now()->subDays(rand(0, 60)),
            'is_featured' => fake()->boolean(20),
        ];
    }
}

