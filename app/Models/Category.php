<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Stub model: Category
 *
 * Demonstrates HasSlug (auto-generates slug from name) and LogsActivity.
 * Extend with your own fields as needed.
 */
class Category extends Model
{
    use HasFactory;
    use HasSlug;
    use LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // ---------------------------------------------------------------------------
    // Slug configuration (spatie/laravel-sluggable)
    // ---------------------------------------------------------------------------

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
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
            ->setDescriptionForEvent(fn (string $eventName): string => "Category {$eventName}");
    }

    // ---------------------------------------------------------------------------
    // Relationships
    // ---------------------------------------------------------------------------

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}


