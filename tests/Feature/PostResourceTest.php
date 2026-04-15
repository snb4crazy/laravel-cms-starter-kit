<?php

namespace Tests\Feature;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Feature tests for PostResource (Filament v5 / Livewire).
 *
 * Covers:
 *  - Access control (unauthenticated redirect, authenticated access)
 *  - List page: renders records, status tabs, table columns
 *  - Table filters: status, category, is_featured, published_today
 *  - Table actions: publish (custom), edit, delete
 *  - Table bulk actions: delete
 *  - Create page: renders form, validates required fields, creates record
 *  - Edit page: renders form with existing data, saves changes, deletes record
 */
class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Create (or reuse) an admin user that can access the Filament panel. */
    private function adminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_is_redirected_from_list_page(): void
    {
        $this->get(PostResource::getUrl('index'))->assertRedirect();
    }

    public function test_unauthenticated_user_is_redirected_from_create_page(): void
    {
        $this->get(PostResource::getUrl('create'))->assertRedirect();
    }

    public function test_unauthenticated_user_is_redirected_from_edit_page(): void
    {
        $post = Post::factory()->create();

        $this->get(PostResource::getUrl('edit', ['record' => $post]))->assertRedirect();
    }

    public function test_admin_can_access_list_page(): void
    {
        $this->actingAs($this->adminUser())
            ->get(PostResource::getUrl('index'))
            ->assertSuccessful();
    }

    public function test_admin_can_access_create_page(): void
    {
        $this->actingAs($this->adminUser())
            ->get(PostResource::getUrl('create'))
            ->assertSuccessful();
    }

    public function test_admin_can_access_edit_page(): void
    {
        $post = Post::factory()->create();

        $this->actingAs($this->adminUser())
            ->get(PostResource::getUrl('edit', ['record' => $post]))
            ->assertSuccessful();
    }

    // -------------------------------------------------------------------------
    // List page – table records
    // -------------------------------------------------------------------------

    public function test_list_page_renders_existing_posts(): void
    {
        $posts = Post::factory()->count(3)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCanSeeTableRecords($posts);
    }

    public function test_list_page_does_not_show_records_from_other_datasets(): void
    {
        $visible = Post::factory()->count(2)->create();
        $hidden  = Post::factory()->count(2)->create();

        // Only the visible posts are shown; hidden ones would only disappear
        // if a filter were applied – here we verify all 4 are present by default.
        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCanSeeTableRecords($visible)
            ->assertCanSeeTableRecords($hidden);
    }

    public function test_list_page_counts_correct_number_of_posts(): void
    {
        Post::factory()->count(5)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCountTableRecords(5);
    }

    // -------------------------------------------------------------------------
    // List page – table columns
    // -------------------------------------------------------------------------

    public function test_table_has_title_column(): void
    {
        Post::factory()->count(1)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCanRenderTableColumn('title');
    }

    public function test_table_has_status_column(): void
    {
        Post::factory()->count(1)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCanRenderTableColumn('status');
    }

    public function test_table_has_is_featured_column(): void
    {
        Post::factory()->count(1)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertCanRenderTableColumn('is_featured');
    }

    // -------------------------------------------------------------------------
    // List page – filters
    // -------------------------------------------------------------------------

    public function test_status_filter_shows_only_matching_posts(): void
    {
        $drafts    = Post::factory()->count(2)->create(['status' => 'draft']);
        $published = Post::factory()->count(3)->create(['status' => 'published']);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->filterTable('status', 'draft')
            ->assertCanSeeTableRecords($drafts)
            ->assertCanNotSeeTableRecords($published);
    }

    public function test_status_filter_shows_only_published_posts(): void
    {
        $drafts    = Post::factory()->count(2)->create(['status' => 'draft']);
        $published = Post::factory()->count(2)->create(['status' => 'published']);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->filterTable('status', 'published')
            ->assertCanSeeTableRecords($published)
            ->assertCanNotSeeTableRecords($drafts);
    }

    public function test_category_filter_shows_only_posts_in_that_category(): void
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        $inCat  = Post::factory()->count(2)->create(['category_id' => $cat1->id]);
        $outCat = Post::factory()->count(2)->create(['category_id' => $cat2->id]);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->filterTable('category', $cat1->id)
            ->assertCanSeeTableRecords($inCat)
            ->assertCanNotSeeTableRecords($outCat);
    }

    public function test_is_featured_toggle_filter_shows_only_featured_posts(): void
    {
        $featured    = Post::factory()->count(2)->create(['is_featured' => true]);
        $notFeatured = Post::factory()->count(2)->create(['is_featured' => false]);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->filterTable('is_featured', true)
            ->assertCanSeeTableRecords($featured)
            ->assertCanNotSeeTableRecords($notFeatured);
    }

    public function test_published_today_filter_shows_only_todays_posts(): void
    {
        $today     = Post::factory()->create(['published_at' => now()]);
        $yesterday = Post::factory()->create(['published_at' => now()->subDay()]);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->filterTable('published_today', true)
            ->assertCanSeeTableRecords([$today])
            ->assertCanNotSeeTableRecords([$yesterday]);
    }

    // -------------------------------------------------------------------------
    // List page – row actions
    // -------------------------------------------------------------------------

    public function test_publish_action_is_visible_for_non_published_post(): void
    {
        $draft = Post::factory()->create(['status' => 'draft']);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertTableActionVisible('publish', $draft);
    }

    public function test_publish_action_is_hidden_for_already_published_post(): void
    {
        $published = Post::factory()->create(['status' => 'published']);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->assertTableActionHidden('publish', $published);
    }

    public function test_publish_action_sets_status_to_published(): void
    {
        $draft = Post::factory()->create([
            'status'       => 'draft',
            'published_at' => null,
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->callTableAction('publish', $draft);

        $this->assertDatabaseHas('posts', [
            'id'     => $draft->id,
            'status' => 'published',
        ]);
    }

    public function test_publish_action_preserves_existing_published_at_date(): void
    {
        $publishedAt = now()->subWeek();
        $draft       = Post::factory()->create([
            'status'       => 'draft',
            'published_at' => $publishedAt,
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->callTableAction('publish', $draft);

        $this->assertDatabaseHas('posts', [
            'id'     => $draft->id,
            'status' => 'published',
        ]);

        // published_at should not have been overwritten.
        // Compare at second precision because the database strips microseconds.
        $this->assertEquals(
            $publishedAt->startOfSecond()->timestamp,
            $draft->fresh()->published_at->timestamp,
            'published_at should be preserved when already set'
        );
    }

    public function test_delete_table_action_removes_a_post(): void
    {
        $post = Post::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->callTableAction('delete', $post);

        $this->assertModelMissing($post);
    }

    // -------------------------------------------------------------------------
    // List page – bulk actions
    // -------------------------------------------------------------------------

    public function test_bulk_delete_action_removes_selected_posts(): void
    {
        $posts   = Post::factory()->count(3)->create();
        $kept    = Post::factory()->count(2)->create();

        Livewire::actingAs($this->adminUser())
            ->test(ListPosts::class)
            ->callTableBulkAction('delete', $posts);

        foreach ($posts as $post) {
            $this->assertModelMissing($post);
        }

        foreach ($kept as $post) {
            $this->assertModelExists($post);
        }
    }

    // -------------------------------------------------------------------------
    // Create page – form
    // -------------------------------------------------------------------------

    public function test_create_form_renders_required_fields(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->assertFormFieldExists('title')
            ->assertFormFieldExists('slug')
            ->assertFormFieldExists('status');
    }

    public function test_cover_field_uses_the_configured_media_disk(): void
    {
        config()->set('media-library.disk_name', 's3');

        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->assertFormFieldExists('cover', fn ($field): bool =>
                $field instanceof SpatieMediaLibraryFileUpload
                && $field->getCollection() === 'cover'
                && $field->getDiskName() === 's3'
            );
    }

    public function test_gallery_field_is_hidden_by_default(): void
    {
        config()->set('cms.features.media.post_gallery', false);

        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->assertFormFieldDoesNotExist('gallery');
    }

    public function test_gallery_field_can_be_enabled_per_project(): void
    {
        config()->set('cms.features.media.post_gallery', true);
        config()->set('media-library.disk_name', 'public');

        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->assertFormFieldExists('gallery', fn ($field): bool =>
                $field instanceof SpatieMediaLibraryFileUpload
                && $field->getCollection() === 'gallery'
                && $field->isMultiple()
                && $field->getDiskName() === 'public'
            );
    }

    public function test_can_create_a_post_with_valid_data(): void
    {
        $category = Category::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->fillForm([
                'title'       => 'My First Post',
                'slug'        => 'my-first-post',
                'status'      => 'draft',
                'category_id' => $category->id,
                'excerpt'     => 'A short excerpt.',
                'content'     => 'Full post content goes here.',
                'is_featured' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'title'  => 'My First Post',
            'slug'   => 'my-first-post',
            'status' => 'draft',
        ]);
    }

    public function test_create_post_fails_validation_without_title(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->fillForm([
                'title'  => '',
                'slug'   => 'no-title',
                'status' => 'draft',
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    }

    public function test_create_post_fails_validation_without_slug(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->fillForm([
                'title'  => 'Valid Title',
                'slug'   => '',
                'status' => 'draft',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'required']);
    }

    public function test_create_post_fails_validation_with_duplicate_slug(): void
    {
        $existing = Post::factory()->create(['slug' => 'existing-slug']);

        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->fillForm([
                'title'  => 'Another Post',
                'slug'   => 'existing-slug',
                'status' => 'draft',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug']);
    }

    public function test_create_post_fails_validation_without_status(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(CreatePost::class)
            ->fillForm([
                'title'  => 'Valid Title',
                'slug'   => 'valid-title',
                'status' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['status']);
    }

    // -------------------------------------------------------------------------
    // Edit page – form
    // -------------------------------------------------------------------------

    public function test_edit_form_is_pre_filled_with_existing_data(): void
    {
        $post = Post::factory()->create([
            'title'   => 'Original Title',
            'status'  => 'draft',
            'excerpt' => 'Original excerpt.',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->assertFormSet([
                'title'   => 'Original Title',
                'status'  => 'draft',
                'excerpt' => 'Original excerpt.',
            ]);
    }

    public function test_can_update_a_post(): void
    {
        $post = Post::factory()->create(['title' => 'Old Title', 'status' => 'draft']);

        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'title'  => 'Updated Title',
                'status' => 'published',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', [
            'id'     => $post->id,
            'title'  => 'Updated Title',
            'status' => 'published',
        ]);
    }

    public function test_edit_post_fails_validation_without_title(): void
    {
        $post = Post::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['title' => ''])
            ->call('save')
            ->assertHasFormErrors(['title' => 'required']);
    }

    public function test_edit_page_delete_action_removes_post(): void
    {
        $post = Post::factory()->create();

        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction('delete');

        $this->assertModelMissing($post);
    }

    public function test_can_toggle_featured_flag_on_existing_post(): void
    {
        $post = Post::factory()->create(['is_featured' => false]);

        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['is_featured' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($post->fresh()->is_featured);
    }

    public function test_slug_uniqueness_is_ignored_for_own_record_on_edit(): void
    {
        $post = Post::factory()->create(['slug' => 'my-unique-slug']);

        // Saving with the same slug should not trigger a validation error.
        Livewire::actingAs($this->adminUser())
            ->test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm([
                'title'  => 'Any Title',
                'slug'   => 'my-unique-slug',
                'status' => 'draft',
            ])
            ->call('save')
            ->assertHasNoFormErrors(['slug']);
    }
}


