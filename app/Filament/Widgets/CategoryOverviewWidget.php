<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CategoryOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Category::query()
                    ->withCount([
                        'posts',
                        'posts as published_posts_count' => fn ($query) => $query->where('status', 'published'),
                    ])
                    ->withMax('posts', 'published_at')
                    ->orderByDesc('posts_count')
                    ->limit(6)
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->url(fn (Category $record): string => CategoryResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('published_posts_count')
                    ->label('Published')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('posts_max_published_at')
                    ->label('Latest publish')
                    ->since()
                    ->placeholder('No published posts'),
            ])
            ->paginated(false);
    }
}

