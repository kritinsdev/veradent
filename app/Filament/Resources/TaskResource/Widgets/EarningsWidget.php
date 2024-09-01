<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class EarningsWidget extends Widget
{
    protected static string $view = 'task-resource.widgets.total-price';

    public function getThisMonthEarnings(): int
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return Task::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');
    }
}
