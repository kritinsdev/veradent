<?php

namespace App\Livewire;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DailyEarningsChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Earnings Overview';
    protected static ?int $sort = 1;
    public ?string $filter = '30days';

    public function getColumnSpan(): int|string|array
    {
        return 12;
    }

    protected function getFilters(): ?array
    {
        return [
            '3months' => 'Last 3 Months',
            '30days' => 'Last 30 Days',
            '7days' => 'Last 7 Days',
        ];
    }

    protected function getData(): array
    {
        $data = match ($this->filter) {
            '7days' => $this->getLastNDaysData(7),
            '30days' => $this->getLastNDaysData(30),
            '3months' => $this->getLastNDaysData(90),
            default => $this->getLastNDaysData(30),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Daily Earnings',
                    'data' => $data['earnings'],
                    'borderColor' => '#4CAF50',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(76, 175, 80, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [];
    }

    private function getLastNDaysData(int $days): array
    {
        $dateRange = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $dateRange->push($date);
        }

        $data = Task::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subDays($days)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $formattedData = $dateRange->map(function ($date) use ($data) {
            $dateStr = $date->format('Y-m-d');
            return [
                'label' => $date->format('M d'),
                'earnings' => $data->get($dateStr)?->total ?? 0,
            ];
        });

        return [
            'labels' => $formattedData->pluck('label')->toArray(),
            'earnings' => $formattedData->pluck('earnings')->toArray(),
        ];
    }
}
