<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EarningsWidget extends Widget
{
    protected static string $view = 'task-resource.widgets.total-price';
    protected static bool $isLazy = false;

    public function getColumnSpan(): int|string|array
    {
        return 12;
    }

    public function getThisMonthEarnings(): int
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return Task::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');
    }

    public function getTodayEarnings(): int
    {
        $today = Carbon::now()->toDateString();

        return Task::whereDate('created_at', $today)
            ->sum('total_price');
    }

    public function getPreviousMonthEarnings(): int
    {
        $previousMonth = Carbon::now()->subMonth()->month;
        $previousMonthYear = Carbon::now()->subMonth()->year;

        return Task::whereMonth('created_at', $previousMonth)
            ->whereYear('created_at', $previousMonthYear)
            ->sum('total_price');
    }

    public function getDailyAverageEarnings(): float
    {
        $currentYear = Carbon::now()->year;

        $dailyEarnings = Task::select(
            DB::raw('DATE(created_at) as day'),
            DB::raw('SUM(total_price) as total_earnings')
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('day')
            ->get();

        $totalDays = $dailyEarnings->count();

        $totalEarnings = $dailyEarnings->sum('total_earnings');

        if ($totalDays == 0) {
            return 0;
        }

        $dailyAverage = $totalEarnings / $totalDays;

        return number_format($dailyAverage, 2);
    }
}
