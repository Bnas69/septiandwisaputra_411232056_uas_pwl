<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi - {{ $sale->transaction_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 13px; color: #000; background: #fff; padding: 24px; }
        .receipt { max-width: 320px; margin: 0 auto; }
        .receipt-header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 12px; margin-bottom: 12px; }
        .receipt-header h2 { font-size: 16px; margin-bottom: 2px; }
        .receipt-header p { font-size: 11px; color: #555; }
        .receipt-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 12px; }
        .receipt-row.total { border-top: 2px dashed #000; margin-top: 8px; padding-top: 8px; font-weight: bold; font-size: 14px; }
        .receipt-section { margin: 12px 0; }
        .receipt-section-title { font-weight: bold; font-size: 11px; text-transform: uppercase; margin-bottom: 4px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        .receipt-footer { text-align: center; border-top: 2px dashed #000; padding-top: 12px; margin-top: 12px; font-size: 11px; color: #555; }
        .receipt-ref { font-size: 10px; color: #888; text-align: center; margin-top: 8px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h2>SmartCatalog</h2>
            <p>Resi Transaksi</p>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Info Transaksi</div>
            <div class="receipt-row"><span>No. Transaksi</span><span>{{ $sale->transaction_number }}</span></div>
            <div class="receipt-row"><span>Tanggal</span><span>{{ $sale->transaction_date?->format('d/m/Y H:i') ?? '-' }}</span></div>
            <div class="receipt-row"><span>Merchant</span><span>{{ $sale->merchant_code }}</span></div>
        </div>

        <div class="receipt-section">
            <div class="receipt-section-title">Item</div>
            <div class="receipt-row"><span>{{ $sale->product?->product_name ?? '-' }}</span><span>x{{ $sale->qty }}</span></div>
            <div class="receipt-row"><span>Harga</span><span>Rp {{ number_format($sale->price, 0, ',', '.') }}</span></div>
            <div class="receipt-row"><span>Subtotal</span><span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span></div>
        </div>

        <div class="receipt-row total"><span>TOTAL</span><span>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span></div>

        <div class="receipt-section" style="margin-top:12px;">
            <div class="receipt-section-title">Pembayaran</div>
            <div class="receipt-row"><span>Metode</span><span>{{ $sale->paymentMethodLabel() }}</span></div>
            <div class="receipt-row"><span>Status</span><span>{{ ucfirst($sale->payment_status) }}</span></div>
            @if($sale->payment_ref)
            <div class="receipt-row"><span>Referensi</span><span>{{ $sale->payment_ref }}</span></div>
            @endif
        </div>

        <div class="receipt-footer">
            <p>Terima kasih atas kunjungan Anda</p>
            <p class="receipt-ref">{{ $sale->transaction_number }} | {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:24px;">
        <button onclick="window.print();" style="padding:8px 24px; font-size:14px; cursor:pointer; border:1px solid #000; border-radius:4px; background:#fff;">Cetak Resi</button>
    </div>
</body>
</html>
