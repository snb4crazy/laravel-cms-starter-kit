<x-filament-widgets::widget>
    <x-filament::section
        heading="Playground Links"
        description="Use these shortcuts to demo CRUD flows, settings, and the boilerplate landing page."
    >
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $card)
                <a
                    href="{{ $card['url'] }}"
                    class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-400 hover:shadow-md dark:border-white/10 dark:bg-gray-900"
                >
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ $card['meta'] }}
                    </div>

                    <div class="mt-2 flex items-end justify-between gap-3">
                        <div>
                            <div class="text-lg font-semibold text-gray-950 dark:text-white">
                                {{ $card['label'] }}
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                {{ $card['description'] }}
                            </p>
                        </div>

                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $card['value'] }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

