<x-filament-widgets::widget>
    <div class="flex flex-row gap-4">
        <x-filament::section class="flex-1 flex flex-col">
            <span class="text-sm text-gray-500 dark:text-gray-400">Previous month</span>
            <div class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                € {{ $this->getPreviousMonthEarnings() }}
            </div>
        </x-filament::section>
        <x-filament::section class="flex-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Current month</span>
            <div class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                € {{ $this->getThisMonthEarnings() }}
            </div>
        </x-filament::section>
        <x-filament::section class="flex-1">
            <span class="text-sm text-gray-500 dark:text-gray-400">Today</span>
            <div class="grid flex-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                € {{ $this->getTodayEarnings() }}
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
