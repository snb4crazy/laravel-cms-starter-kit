<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class QuickLinksWidget extends Widget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.quick-links-widget';

    protected function getViewData(): array
    {
        $settings = Cache::get('cms.settings', []);

        return [
            'cards' => [
                [
                    'label' => 'Create Post',
                    'description' => 'Open the resource create page and test the full content workflow.',
                    'value' => Post::count(),
                    'meta' => 'total posts',
                    'url' => PostResource::getUrl('create'),
                ],
                [
                    'label' => 'Create Category',
                    'description' => 'Add taxonomy and instantly see the dashboard category widgets update.',
                    'value' => Category::count(),
                    'meta' => 'total categories',
                    'url' => CategoryResource::getUrl('create'),
                ],
                [
                    'label' => 'Open Site Settings',
                    'description' => 'Try the cached settings flow and maintenance mode toggle.',
                    'value' => ! empty($settings['maintenance_mode']) ? 'ON' : 'OFF',
                    'meta' => 'maintenance mode',
                    'url' => SiteSettings::getUrl(),
                ],
                [
                    'label' => 'Open Docs Index',
                    'description' => 'Review architecture and install notes while experimenting with widgets.',
                    'value' => 'docs',
                    'meta' => 'project notes',
                    'url' => url('/'),
                ],
            ],
        ];
    }
}

