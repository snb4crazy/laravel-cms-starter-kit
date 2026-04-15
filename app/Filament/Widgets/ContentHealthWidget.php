<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentHealthWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $uncategorizedPosts = Post::whereNull('category_id')->count();
        $featuredPosts = Post::where('is_featured', true)->count();
        $emptyCategories = Category::doesntHave('posts')->count();
        $stalePublishedPosts = Post::where('status', 'published')
            ->where('updated_at', '<', now()->subDays(30))
            ->count();

        return [
            Stat::make('Uncategorized Posts', $uncategorizedPosts)
                ->description('Posts missing a category assignment')
                ->icon('heroicon-o-folder-open')
                ->color($uncategorizedPosts > 0 ? 'warning' : 'success'),

            Stat::make('Featured Posts', $featuredPosts)
                ->description('Content highlighted on the frontend')
                ->icon('heroicon-o-star')
                ->color('info'),

            Stat::make('Empty Categories', $emptyCategories)
                ->description('Useful for cleanup or future planning')
                ->icon('heroicon-o-tag')
                ->color($emptyCategories > 0 ? 'gray' : 'success'),

            Stat::make('Stale Published Content', $stalePublishedPosts)
                ->description('Published posts untouched for 30+ days')
                ->icon('heroicon-o-clock')
                ->color($stalePublishedPosts > 0 ? 'danger' : 'success'),
        ];
    }
}

