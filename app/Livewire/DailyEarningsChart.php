<?php

namespace App\Livewire;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DailyEarningsChart extends ChartWidget
{
    protected static ?string $heading = 'Earnings Overview';
    protected static ?int $sort = 1;
    public ?string $filter = 'currentMonth';

    public function getColumnSpan(): int|string|array
    {
        return 12;
    }

    protected function getFilters(): ?array
    {
        return [
            'currentYear' => 'Current year',
            'previousMonth' => 'Previous month',
            'currentMonth' => 'Current month',
        ];
    }

    protected function getData(): array
    {
        $data = match ($this->filter) {
            'currentMonth' => $this->getCurrentMonthData(),
            'previousMonth' => $this->getPreviousMonthData(),
            'currentYear' => $this->getCurrentYearData(),
            default => $this->getCurrentMonthData(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Earnings',
                    'data' => $data['earnings'],
                    'borderColor' => 'rgba(217, 119, 6, 1)',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.1)',
                    'tension' => 0.2,
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

    private function getCurrentMonthData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $dateRange = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateRange->push($date->copy());
        }

        $data = Task::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        )
            ->whereBetween('created_at', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])
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

    private function getPreviousMonthData(): array
    {
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        $dateRange = collect();
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dateRange->push($date->copy());
        }

        $data = Task::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total')
        )
            ->whereBetween('created_at', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])
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

    private function getCurrentYearData(): array
    {
        $currentYear = Carbon::now()->year;
        $months = collect();

        for ($month = 1; $month <= 12; $month++) {
            $months->push(Carbon::createFromDate($currentYear, $month, 1));
        }

        $data = Task::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total')
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $formattedData = $months->map(function ($date) use ($data) {
            $monthNumber = (int)$date->format('n');
            return [
                'label' => $date->format('M'),
                'earnings' => $data->get($monthNumber)?->total ?? 0,
            ];
        });

        return [
            'labels' => $formattedData->pluck('label')->toArray(),
            'earnings' => $formattedData->pluck('earnings')->toArray(),
        ];
    }
}
