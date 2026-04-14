<x-filament-panels::page>
    {{--
        Stub Blade view for the SiteSettings custom page.

        x-filament-panels::page  renders the standard Filament page chrome
        (breadcrumbs, header, notifications area).

        x-filament::form         renders the form defined in SiteSettings::form().
        wire:submit="save"       calls the save() Livewire action on submit.
    --}}

    <x-filament::form wire:submit="save">

        {{ $this->form }}

        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit">
                Save settings
            </x-filament::button>
        </div>

    </x-filament::form>
</x-filament-panels::page>

