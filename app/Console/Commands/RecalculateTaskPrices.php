<?php

namespace App\Console\Commands;

use App\Enums\Type;
use App\Models\Task;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RecalculateTaskPrices extends Command
{
    protected $signature = 'tasks:recalculate-prices
                            {month : The month to recalculate (1-12)}
                            {year? : The year to recalculate (defaults to current year)}
                            {--full-form-implant=12 : New price for full form with implant}
                            {--full-form-no-implant=10 : New price for full form without implant}
                            {--onlay=10 : New price for onlay}';

    protected $description = 'Recalculate total prices for tasks in a specific month with updated pricing';

    public function handle()
    {
        $month = $this->argument('month');
        $year = $this->argument('year') ?? now()->year;

        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12');
            return 1;
        }

        $fullFormImplantPrice = $this->option('full-form-implant');
        $fullFormNoImplantPrice = $this->option('full-form-no-implant');
        $onlayPrice = $this->option('onlay');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $tasks = Task::whereBetween('created_at', [$startDate, $endDate])
                     ->with('sections')
                     ->get();

        if ($tasks->isEmpty()) {
            $this->info("No tasks found for {$startDate->format('F Y')}");
            return 0;
        }

        $this->info("Found {$tasks->count()} tasks for {$startDate->format('F Y')}");

        $updated = 0;
        foreach ($tasks as $task) {
            $totalPrice = $this->calculateTaskPrice($task, $fullFormImplantPrice, $fullFormNoImplantPrice, $onlayPrice);

            if ($task->total_price != $totalPrice) {
                $oldPrice = $task->total_price;
                $task->update(['total_price' => $totalPrice]);
                $this->line("Task #{$task->id}: €{$oldPrice} → €{$totalPrice}");
                $updated++;
            }
        }

        $this->info("Updated {$updated} tasks with new pricing");
        return 0;
    }

    private function calculateTaskPrice(
        Task $task,
        int $fullFormImplantPrice,
        int $fullFormNoImplantPrice,
        int $onlayPrice
    ): float
    {
        $totalPrice = 0;

        if ($task->scan_models > 0) {
            $totalPrice += $task->scan_models * 3;
        }

        if ($task->{'3d_models'} > 0) {
            $multiplier = $task->{'3d_models_full'} ? 3 : 1.5;
            $totalPrice += $task->{'3d_models'} * $multiplier;
        }

        foreach ($task->sections as $section) {
            $price = match ($section->type) {
                Type::FULL_FORM_IMPLANT => $fullFormImplantPrice,
                Type::FULL_FORM_NO_IMPLANT => $fullFormNoImplantPrice,
                Type::ONLAY => $onlayPrice,
                default => $section->type->price()
            };
            $totalPrice += $price;
        }

        return $totalPrice;
    }
}
