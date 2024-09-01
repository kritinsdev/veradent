<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-xl font-semibold">Earned this month</h2>
        <p class="text-3xl font-bold">€ {{ $this->getThisMonthEarnings() }}</p>
    </x-filament::section>
</x-filament-widgets::widget>
