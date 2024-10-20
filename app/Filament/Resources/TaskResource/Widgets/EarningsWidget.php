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

    public function getWeeklyAverageEarnings(): float
    {
        $currentYear = Carbon::now()->year;

        $weeklyAverages = Task::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('WEEK(created_at) as week'),
            DB::raw('AVG(total_price) as weekly_average')
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('year', 'week')
            ->get();

        $totalWeeklyAverage = $weeklyAverages->avg('weekly_average');

        return number_format($totalWeeklyAverage ?? 0, 2);
    }

    public function getDailyAverageEarnings(): float
    {
        $currentYear = Carbon::now()->year;

        $dailyAverages = Task::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('DAYOFYEAR(created_at) as day_of_year'),
            DB::raw('AVG(total_price) as daily_average')
        )
            ->whereYear('created_at', $currentYear)
            ->groupBy('year', 'day_of_year')
            ->get();

        $totalDailyAverage = $dailyAverages->avg('daily_average');

        return number_format($totalDailyAverage ?? 0, 2);
    }
}
