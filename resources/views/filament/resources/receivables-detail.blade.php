<div class="space-y-6">
    {{-- <h2 class="text-lg font-bold">Detail Semua Piutang - {{ $customer->name }}</h2> --}}

    @foreach ($transactions as $transaction)
        <div class="border border-gray-300 dark:border-gray-700 p-4 rounded-md shadow-sm bg-white dark:bg-gray-800">
            {{-- Gunakan blade detail lama --}}
            @include('filament.resources.transaction-detail', ['record' => $transaction])
        </div>
    @endforeach

    @php
        $totalPiutang = $transactions->sum(function ($trx) {
            $dibayar = ($trx->initial_payment ?? 0) + ($trx->receivablePayments->sum('amount') ?? 0);
            return max($trx->total - $dibayar, 0);
        });
    @endphp

    <div class="text-right font-semibold text-green-500 mt-4">
        Total Piutang Keseluruhan: Rp <span class="text-xl">{{ number_format($totalPiutang, 0, ',', '.') }}</span>
    </div>

</div>