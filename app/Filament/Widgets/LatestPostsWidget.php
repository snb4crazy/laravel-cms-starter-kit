<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

/**
 * Stub Widget: Latest Posts Table
 *
 * Demonstrates a TableWidget — shows a mini table on the dashboard
 * with a subset of data. The table uses the same builder as a full Resource.
 */
class LatestPostsWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('category.name')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'archived'  => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('updated_at')
                    ->label('Last edited')
                    ->since(),
            ])
            ->paginated(false);
    }
}

