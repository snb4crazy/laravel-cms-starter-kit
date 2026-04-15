<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Attributes & casts
    // -------------------------------------------------------------------------

    public function test_post_has_expected_fillable_attributes(): void
    {
        $expected = [
            'category_id',
            'title',
            'slug',
            'excerpt',
            'content',
            'status',
            'published_at',
            'is_featured',
        ];

        $this->assertSame($expected, (new Post)->getFillable());
    }

    public function test_published_at_is_cast_to_datetime(): void
    {
        $casts = (new Post)->getCasts();

        $this->assertArrayHasKey('published_at', $casts);
        $this->assertSame('datetime', $casts['published_at']);
    }

    public function test_is_featured_is_cast_to_boolean(): void
    {
        $casts = (new Post)->getCasts();

        $this->assertArrayHasKey('is_featured', $casts);
        $this->assertSame('boolean', $casts['is_featured']);
    }

    // -------------------------------------------------------------------------
    // Slug
    // -------------------------------------------------------------------------

    public function test_slug_is_auto_generated_from_title(): void
    {
        $post = Post::factory()->create(['title' => 'Hello World', 'slug' => 'hello-world']);

        $this->assertSame('hello-world', $post->slug);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function test_post_belongs_to_a_category(): void
    {
        $category = Category::factory()->create();
        $post     = Post::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $post->category);
        $this->assertEquals($category->id, $post->category->id);
    }

    public function test_post_may_have_no_category(): void
    {
        $post = Post::factory()->create(['category_id' => null]);

        $this->assertNull($post->category);
    }

    // -------------------------------------------------------------------------
    // Query scopes
    // -------------------------------------------------------------------------

    public function test_scope_published_returns_only_published_posts(): void
    {
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);
        Post::factory()->create(['status' => 'archived']);

        $results = Post::published()->get();

        $this->assertCount(1, $results);
        $this->assertSame('published', $results->first()->status);
    }

    public function test_scope_draft_returns_only_draft_posts(): void
    {
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);
        Post::factory()->create(['status' => 'draft']);

        $results = Post::draft()->get();

        $this->assertCount(2, $results);
        foreach ($results as $post) {
            $this->assertSame('draft', $post->status);
        }
    }

    public function test_scope_featured_returns_only_featured_posts(): void
    {
        Post::factory()->create(['is_featured' => true]);
        Post::factory()->create(['is_featured' => true]);
        Post::factory()->create(['is_featured' => false]);

        $results = Post::featured()->get();

        $this->assertCount(2, $results);
        foreach ($results as $post) {
            $this->assertTrue($post->is_featured);
        }
    }

    public function test_scope_featured_excludes_non_featured_posts(): void
    {
        Post::factory()->count(3)->create(['is_featured' => false]);

        $this->assertCount(0, Post::featured()->get());
    }

    public function test_scopes_can_be_chained(): void
    {
        Post::factory()->create(['status' => 'published', 'is_featured' => true]);
        Post::factory()->create(['status' => 'published', 'is_featured' => false]);
        Post::factory()->create(['status' => 'draft',     'is_featured' => true]);

        $results = Post::published()->featured()->get();

        $this->assertCount(1, $results);
        $this->assertSame('published', $results->first()->status);
        $this->assertTrue($results->first()->is_featured);
    }
}

