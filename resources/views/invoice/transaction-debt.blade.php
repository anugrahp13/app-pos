<!DOCTYPE html>
<html>
<head>
    <title>Struk Piutang</title>
    <style>
        @page {
            size: 72mm auto;
            margin: 0;
        }
        body {
            font-family: monospace;
            font-size: 13px;
            margin: 0;
            padding: 0 9px;
            width: 300px;
        }
        .container {
            padding-bottom: 0;
        }
        .size-title {
            font-size: 18px
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .product {
            font-size: 15px
        }
        .line { 
            border-top: 1px dashed black; 
            margin: 8px 0; 
        }

        table { 
            width: 100%;
            line-height: 10px;
        }
        td { vertical-align: top; }
        .right {
            text-align: right;
        }

        .mt-2 {
            margin-top: 8px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
        }

        .item-name {
            flex: 1;
            word-break: break-word;
        }

        .item-price {
            text-align: right;
            white-space: nowrap;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="size-title bold center">HAFNAN MART</div>
        <div class="center">GG Haji Matali No.74 Jakarta Timur</div>
        <div class="line"></div>
        
        <div>No. HFM-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div style="display: flex; justify-content: space-between;">
            <div>
                <div>{{ $transaction->created_at->format('Y-m-d') }}</div>
                <div>{{ $transaction->created_at->format('H:i:s') }}</div>
            </div>
            <div style="text-align: right;">
                <div>Kasir: {{ $transaction->user->name }}</div>
                <div>{{ $transaction->customer->name ?? '-' }}</div>
            </div>
        </div>
        <div class="line"></div>

        @foreach($transaction->items as $item)
            <div class="item-name bold"> {{ strtoupper($item->product->name ?? $item->name ?? '-') }}</div>
            <div class="product item-row">
                <div>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                <div class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
        @endforeach

        <div class="line"></div>

        <div>
            Total QTY : {{ $transaction->items->sum('quantity') }} <br>
            Sub Total <span style="float:right">Rp {{ number_format($transaction->total,0,',','.') }}</span>
        </div>
    
        <div class="bold">
            Total <span style="float:right">Rp {{ number_format($transaction->total,0,',','.') }}</span>
        </div>
        <div class="line"></div>
    
        {{-- Bagian tambahan khusus Piutang --}}
        <div>
            Tipe: kredit 
            <span style="float:right">
                Jatuh Tempo: {{ $transaction->due_date ? \Carbon\Carbon::parse($transaction->due_date)->translatedFormat('d-m-Y') : '-' }}
            </span><br>

            @php
                $statusMap = [
                    'unpaid'  => 'Belum Lunas',
                    'partial' => 'Sebagian',
                    'paid'    => 'Lunas',
                ];
                $statusLabel = $statusMap[$transaction->status] ?? ucfirst($transaction->status);
            @endphp
            Status: {{ $statusLabel }} <br>
            @php
                $paid = $transaction->initial_payment + $transaction->receivablePayments()->sum('amount');
            @endphp

            @if($transaction->initial_payment > 0)
                Dibayar Awal: Rp {{ number_format($transaction->initial_payment,0,',','.') }} <br>
            @endif

            @if($transaction->receivablePayments()->sum('amount') > 0)
                Pembayaran Tambahan: Rp {{ number_format($transaction->receivablePayments()->sum('amount'),0,',','.') }} <br>
            @endif

            Sisa kredit: Rp {{ number_format($transaction->total - $paid,0,',','.') }}
        </div>

        <div class="line"></div>
        <div class="center">
            WhatsApp ke nomor ini 0813-8052-5404 untuk membuat pesanan.<br>
        </div>

        <div class="center bold mt-2">===== TERIMA KASIH =====</div>
        <div class="footer-space"></div>
    </div>
    <style>
        @media print {
            .footer-space {
                margin-top: 70px;
                height: 70px;
            }
        }
    </style>
    <script>
        window.onload = () => {
            window.print();
            setTimeout(() => window.close(), 300);
        };
    </script>
    
</body>
</html>
