<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Stub Widget: Post Stats Overview
 *
 * Demonstrates StatsOverviewWidget — the number cards shown at the top
 * of the Filament dashboard.
 *
 * Each Stat() can have:
 *  - value (the big number)
 *  - description (the small label below it)
 *  - icon
 *  - color
 *  - chart (sparkline data as an array of numbers)
 */
class PostStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Post::count();
        $published = Post::where('status', 'published')->count();
        $drafts = Post::where('status', 'draft')->count();
        $archived = Post::where('status', 'archived')->count();

        $recentTotals = collect(range(6, 0))
            ->map(fn (int $daysAgo): int => Post::whereDate('created_at', now()->subDays($daysAgo))->count())
            ->push(Post::whereDate('created_at', today())->count())
            ->all();

        $recentPublished = collect(range(6, 0))
            ->map(fn (int $daysAgo): int => Post::where('status', 'published')->whereDate('created_at', now()->subDays($daysAgo))->count())
            ->push(Post::where('status', 'published')->whereDate('created_at', today())->count())
            ->all();

        return [
            Stat::make('Total Posts', $total)
                ->description('All posts in the system')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->chart($recentTotals),

            Stat::make('Published', $published)
                ->description('Live on the site')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->chart($recentPublished),

            Stat::make('Drafts', $drafts)
                ->description('Pending review or editing')
                ->icon('heroicon-o-pencil')
                ->color('warning'),

            Stat::make('Archived', $archived)
                ->description('Content retained for reference')
                ->icon('heroicon-o-archive-box')
                ->color('danger'),
        ];
    }
}

