<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Activity::query()->latest()->limit(8)
            )
            ->columns([
                TextColumn::make('description')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : 'System')
                    ->badge()
                    ->color('info'),

                TextColumn::make('causer.name')
                    ->label('Actor')
                    ->placeholder('System'),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since(),
            ])
            ->paginated(false);
    }
}

