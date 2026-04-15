<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Category;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Stub Filament Resource: Post
 *
 * Demonstrates every major Filament v5 concept in one file:
 *
 *  Form builder (Schema API)
 *   - TextInput, Textarea, MarkdownEditor, Select, Toggle, DateTimePicker
 *   - SpatieMediaLibraryFileUpload (cover image via media library)
 *   - Section + Grid (from Filament\Schemas\Components in v5)
 *   - Live slug generation from title
 *
 *  Table builder
 *   - ImageColumn, TextColumn, IconColumn with badge colours
 *   - Sortable, searchable, toggleable columns
 *   - SelectFilter, custom Filter with toggle
 *   - Row Actions: Edit, Delete, custom "Publish" action
 *   - Bulk actions: delete
 *
 *  Pages
 *   - List (with status tabs), Create, Edit
 *
 *  Relation managers
 *   - See PostResource/RelationManagers/CommentsRelationManager.php (stub)
 */
class PostResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'title';

    // Filament v5: use methods for icon/group to avoid BackedEnum|string|null type conflicts.
    public static function getModel(): string                            { return Post::class; }
    public static function getNavigationIcon(): string|\BackedEnum|null  { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): ?string                 { return 'Content'; }
    public static function getNavigationSort(): ?int                     { return 1; }

    // ---------------------------------------------------------------------------
    // Form schema (Filament v5: Schema replaces Form)
    // ---------------------------------------------------------------------------

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            // ----  Main content  ------------------------------------------------
            Section::make('Content')
                ->description('The main content fields for this post.')
                ->collapsible()
                ->schema([

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $context, $state, callable $set): void {
                            if ($context === 'create') {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(Post::class, 'slug', ignoreRecord: true)
                        ->helperText('Auto-filled from title. Edit if needed.'),

                    Textarea::make('excerpt')
                        ->rows(2)
                        ->maxLength(500)
                        ->helperText('Short summary shown in listings.')
                        ->columnSpanFull(),

                    MarkdownEditor::make('content')
                        ->toolbarButtons([
                            'attachFiles', 'blockquote', 'bold', 'bulletList',
                            'codeBlock', 'heading', 'italic', 'link',
                            'orderedList', 'redo', 'strike', 'table', 'undo',
                        ])
                        ->columnSpanFull(),

                ]),

            // ----  Cover image  -------------------------------------------------
            Section::make('Cover Image')
                ->collapsible()
                ->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label('Cover image')
                        ->collection('cover')
                        ->image()
                        ->imageEditor()
                        ->maxSize(5120)
                        ->disk('public')
                        ->responsiveImages()
                        ->helperText('Recommended size: 1200 × 630 px.'),
                ]),

            // ----  Publishing settings  -----------------------------------------
            Section::make('Publishing')
                ->collapsible()
                ->schema([

                    Grid::make(2)->schema([

                        Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'published' => 'Published',
                                'archived'  => 'Archived',
                            ])
                            ->default('draft')
                            ->required(),

                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                            ])
                            ->nullable(),

                    ]),

                    Grid::make(2)->schema([

                        DateTimePicker::make('published_at')
                            ->label('Publish at')
                            ->nullable()
                            ->helperText('Leave blank to publish immediately when status = published.'),

                        Toggle::make('is_featured')
                            ->label('Featured post')
                            ->helperText('Featured posts appear in highlighted sections.'),

                    ]),
                ]),

        ]);
    }

    // ---------------------------------------------------------------------------
    // Table schema
    // ---------------------------------------------------------------------------

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                SpatieMediaLibraryImageColumn::make('cover')
                    ->label('')
                    ->collection('cover')
                    ->circular(false)
                    ->size(48)
                    ->defaultImageUrl(asset('favicon.ico'))
                    ->toggleable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn (Post $record): ?string => $record->excerpt ? Str::limit($record->excerpt, 60) : null),

                TextColumn::make('category.name')
                    ->sortable()
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

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not scheduled')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Last edited')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),

                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('is_featured')
                    ->label('Featured only')
                    ->query(fn (Builder $query): Builder => $query->where('is_featured', true))
                    ->toggle(),

                Filter::make('published_today')
                    ->label('Published today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('published_at', today()))
                    ->toggle(),

            ])
            ->filtersFormColumns(2)
            ->actions([

                // Custom action: one-click Publish
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->visible(fn (Post $record): bool => $record->status !== 'published')
                    ->requiresConfirmation()
                    ->action(function (Post $record): void {
                        $record->update([
                            'status'       => 'published',
                            'published_at' => $record->published_at ?? now(),
                        ]);
                        Notification::make()->title('Post published!')->success()->send();
                    }),

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
    // Relation managers
    // ---------------------------------------------------------------------------

    public static function getRelationManagers(): array
    {
        return [
            // Uncomment to activate the comments relation panel on edit page:
            // RelationManagers\CommentsRelationManager::class,
        ];
    }

    // ---------------------------------------------------------------------------
    // Pages
    // ---------------------------------------------------------------------------

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit'   => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}

