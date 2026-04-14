<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Stub model: Post
 *
 * Demonstrates:
 * - HasSlug      → auto-generates slug from title
 * - LogsActivity → records create/update/delete in activity_log table
 * - HasMedia     → attach images/files via spatie/laravel-medialibrary
 *
 * Status values: draft | published | archived
 */
class Post extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'is_featured',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    // ---------------------------------------------------------------------------
    // Slug configuration (spatie/laravel-sluggable)
    // ---------------------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // ---------------------------------------------------------------------------
    // Activity log configuration (spatie/laravel-activitylog)
    // ---------------------------------------------------------------------------

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName): string => "Post {$eventName}");
    }

    // ---------------------------------------------------------------------------
    // Media library collections (spatie/laravel-medialibrary)
    //
    // Define as many collections as needed.
    // Conversions registered here are run on upload.
    // ---------------------------------------------------------------------------

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->singleFile();  // only one cover image per post

        $this->addMediaCollection('gallery');  // multiple images allowed
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->performOnCollections('cover', 'gallery');

        $this->addMediaConversion('banner')
            ->width(1200)
            ->height(630)
            ->performOnCollections('cover');
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ---------------------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------------------

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}


