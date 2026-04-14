<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Stub Filament Custom Page: Site Settings
 *
 * Demonstrates how to create a fully custom Filament admin page.
 *
 * Custom pages can:
 *  - Render any Blade view
 *  - Include a Form using HasForms / InteractsWithForms
 *  - Be placed in any navigation group
 *  - Have header actions (buttons in top right)
 *
 * Filament v5 notes:
 *  - form(Schema $schema): Schema  (not Form $form any more)
 *  - Section lives in Filament\Schemas\Components
 *  - $navigationIcon / $navigationGroup must use getter methods
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $title           = 'Site Settings';
    protected static ?int    $navigationSort  = 10;

    // Filament v5: use methods instead of typed properties.
    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-cog-6-tooth'; }
    public static function getNavigationGroup(): ?string                { return 'System'; }

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name'        => config('app.name'),
            'site_tagline'     => '',
            'maintenance_mode' => false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->required(),

                        TextInput::make('site_tagline')
                            ->label('Tagline')
                            ->nullable(),

                        Toggle::make('maintenance_mode')
                            ->label('Maintenance mode')
                            ->helperText('When enabled, only admins can access the front end.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // In production: persist $state to your settings store.
        // E.g. Settings::set('site_name', $state['site_name']);

        Notification::make()
            ->title('Settings saved!')
            ->success()
            ->send();
    }
}


