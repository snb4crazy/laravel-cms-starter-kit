<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

/**
 * Stub RelationManager: Comments
 *
 * Demonstrates how to add a related records panel on the Post edit page.
 * To activate: uncomment in PostResource::getRelationManagers().
 *
 * What relation managers can do:
 *  - Show a table of related records directly on the parent's edit page
 *  - Allow create / edit / delete of related records inline
 *  - Support full form and table builder just like a Resource
 */
class CommentsRelationManager extends RelationManager
{
    // This must match the relationship method name on Post (e.g. Post::comments())
    protected static string $relationship = 'comments';

    protected static ?string $title = 'Comments';

    // ---- Form: fields to create/edit a comment ---------------------------------

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Add form fields here, e.g.:
            // TextInput::make('author')->required(),
            // Textarea::make('body')->required(),
        ]);
    }

    // ---- Table: list related records ------------------------------------------

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                // Add columns here, e.g.:
                // TextColumn::make('author'),
                // TextColumn::make('body')->limit(60),
                // TextColumn::make('created_at')->since(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
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
}
