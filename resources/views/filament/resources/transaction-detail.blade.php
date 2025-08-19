<div class="space-y-4 text-sm">
    <div class="grid gap-2 w-full max-w-md" style="grid-template-columns: max-content 1fr;">
        <div class="contents">
            <div class="font-semibold">No. Transaksi:</div>
            <div>HFM-{{ str_pad($record->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="contents">
            <div class="font-semibold">Tanggal:</div>
            <div>{{ $record->created_at->format('d M Y H:i') }} WIB</div>
        </div>
        <div class="contents">
            <div class="font-semibold">Pelanggan:</div>
            <div>{{ $record->customer?->name ?? '-' }}</div>
        </div>
        <div class="contents">
            <div class="font-semibold">Dibuat oleh:</div>
            <div>{{ $record->user?->name ?? '-' }}</div>
        </div>
    </div>    
    <table class="w-full text-left border border-gray-200 dark:border-gray-700 text-sm">
        <thead class="bg-gray-100 dark:bg-gray-800">
            <tr>
                <th class="px-3 py-2 border">Name</th>
                <th class="px-3 py-2 border">Qty</th>
                <th class="px-3 py-2 border">Price</th>
                <th class="px-3 py-2 border">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalQty = 0;
                $totalProducts = 0;
            @endphp
            @foreach ($record->items as $item)
                @php 
                    $totalQty += $item->quantity; 
                    $totalProducts++; 
                @endphp
                <tr class="border-t dark:border-gray-700">
                    <td class="px-3 py-2 border">{{ $item->product?->name ?? '-' }}</td>
                    <td class="px-3 py-2 border">{{ $item->quantity }}</td>
                    <td class="px-3 py-2 border">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 border">Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex items-center justify-between">
        <div class="flex flex-col">
            <div class="text-right font-semibold mt-2">
                Total Nama Produk: {{ $totalProducts }}
            </div>
            <div class="text-right font-semibold mt-2">
                Total Qty: {{ $totalQty }}
            </div>
        </div>
    
        <div class="flex flex-col">
            <div class="text-right font-semibold">
                Bayar (Cash): Rp {{ number_format($record->paid ?? 0, 0, ',', '.') }}
            </div>
            <div class="text-right font-semibold">
                Total: Rp {{ number_format($record->total, 0, ',', '.') }}
            </div>
        </div>
    </div>
    @php
        $isDebt = $record->is_debt;
        $status = $record->status;
        $total = $record->total ?? 0;

        // Jumlah semua pembayaran piutang
        $receivableSum = $record->receivablePayments->sum('amount');

        // Cek apakah initial_payment sudah termasuk di dalam receivablePayments
        // Asumsikan: jika receivableSum >= total → berarti sudah lunas & initial sudah termasuk
        $initialPayment = $record->initial_payment ?? 0;

        // Jika receivableSum >= total OR receivableSum >= initialPayment
        $initialIncluded = $receivableSum >= $total || $receivableSum >= $initialPayment;

        $paid = $isDebt
            ? ($initialIncluded ? $receivableSum : $receivableSum + $initialPayment)
            : ($record->paid ?? 0);
    @endphp
    @if ($isDebt && $status !== 'paid')
        <div class="text-right font-semibold text-red-600">
            Piutang: Rp {{ number_format(max($total - $paid, 0), 0, ',', '.') }}
        </div>
    @elseif ($isDebt && $status === 'paid')
        <div class="text-right font-semibold text-green-600">
            Piutang Lunas: Rp {{ number_format(max($paid - $total, 0), 0, ',', '.') }}
        </div>
    @else
        <div class="text-right font-semibold">
            Change: Rp {{ number_format(max($paid - $total, 0), 0, ',', '.') }}
        </div>
    @endif


</div>
