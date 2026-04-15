<x-filament-panels::page>
    {{--
        Stub Blade view for the SiteSettings custom page.

        x-filament-panels::page renders the standard Filament page chrome
        (breadcrumbs, header, notifications area).

        For custom pages in this template, render the schema with {{ $this->form }}
        and use a native <form> wrapper for Livewire submit handling.
    --}}

    <form wire:submit="save">

        {{ $this->form }}

        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit">
                Save settings
            </x-filament::button>
        </div>

    </form>
</x-filament-panels::page>

