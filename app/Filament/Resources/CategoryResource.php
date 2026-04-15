<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Stub Filament Resource: Category
 *
 * Demonstrates:
 * - Basic form with auto-slug field (Schema/form builder v5 API)
 * - Table with sortable/searchable columns
 * - Inline table actions (edit, delete)
 *
 * Filament v5: form() takes Schema, table() takes Table.
 * Navigation icon/group must be returned via methods (not typed properties).
 */
class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    // Filament v5: use methods — typed properties conflict with BackedEnum|string|null parent types.
    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-tag'; }
    public static function getNavigationGroup(): ?string                { return 'Content'; }
    public static function getNavigationSort(): ?int                    { return 2; }

    // ---------------------------------------------------------------------------
    // Form schema (Filament v5: Schema replaces Form)
    // ---------------------------------------------------------------------------

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (string $context, $state, callable $set): void {
                    if ($context === 'create') {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }
                }),

            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(Category::class, 'slug', ignoreRecord: true)
                ->helperText('Auto-filled from name. You can override it.'),

            Textarea::make('description')
                ->rows(3)
                ->maxLength(1000)
                ->columnSpanFull(),

        ]);
    }

    // ---------------------------------------------------------------------------
    // Table schema
    // ---------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ---------------------------------------------------------------------------
    // Pages
    // ---------------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
