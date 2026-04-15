<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PublishingTrendChartWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Publishing Trend';

    protected ?string $description = 'Track how much content is being created over time.';

    protected string $color = 'success';

    protected ?string $maxHeight = '280px';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
        ];
    }

    protected function getData(): array
    {
        $days = (int) ($this->filter ?: 30);
        $start = now()->subDays($days - 1)->startOfDay();

        $postsByDate = Post::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $labels = [];
        $data = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            $labels[] = $date->format($days > 30 ? 'M j' : 'D');
            $data[] = (int) ($postsByDate[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Posts created',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }
}

