<x-filament::page>
    @php
        // Simpan tanggal ke session agar tetap tersedia saat Livewire reload
        if (request('date_from')) {
            session(['filter_date_from' => request('date_from')]);
        }
        if (request('date_to')) {
            session(['filter_date_to' => request('date_to')]);
        }

        $dateFrom = session('filter_date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = session('filter_date_to', now()->endOfMonth()->format('Y-m-d'));
    @endphp

    {{-- Form filter tanggal --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label for="date_from" class="block text-sm font-medium">Start date</label>
            <input
                type="date"
                name="date_from"
                id="date_from"
                value="{{ $dateFrom }}"
                class="mt-1 block w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
        </div>

        <div>
            <label for="date_to" class="block text-sm font-medium">End date</label>
            <input
                type="date"
                name="date_to"
                id="date_to"
                value="{{ $dateTo }}"
                class="mt-1 block w-full px-3 py-2 bg-gray-900 border border-gray-700 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
        </div>
    </form>    

    {{-- Baris 1: DashboardOverview (lebar penuh) --}}
    <div class="grid grid-cols-1 gap-4">
        @livewire(App\Filament\Widgets\DashboardOverview::class)
    </div>

    {{-- Baris 2: TransactionChart & CustomerCheckout (berdampingan) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        @livewire(App\Filament\Widgets\TransactionChart::class)
        @livewire(App\Filament\Widgets\CustomerCheckout::class)
    </div>

    {{-- Baris 3: LowStockProducts (lebar penuh) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        @livewire(App\Filament\Widgets\LowStockProducts::class)
        @livewire(App\Filament\Widgets\BestSellingProducts::class)
    </div>

    <script>
            // Otomatis Submit dateFrom and dateTo
            document.addEventListener('DOMContentLoaded', function () {
                const from = document.querySelector('#date_from');
                const to = document.querySelector('#date_to');

                from.addEventListener('change', () => from.form.submit());
                to.addEventListener('change', () => to.form.submit());
            });
        </script>
</x-filament::page>
