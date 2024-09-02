<div class="flex gap-4">
    <div class="border border-1 rounded p-2 text-md font-semibold">
        Pagājušais mēnesis: € {{ $this->getPreviousMonthEarnings() }}
    </div>
    <div class="border border-1 rounded p-2 text-md font-semibold">
        Šomēnes: € {{ $this->getThisMonthEarnings() }}
    </div>
    <div class="border border-1 rounded p-2 text-md font-semibold">
        Šodien: € {{ $this->getTodayEarnings() }}
    </div>
</div>
