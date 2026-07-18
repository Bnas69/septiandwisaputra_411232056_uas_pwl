<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - SmartCatalog</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, Helvetica, sans-serif; font-size: 12px; color: #0f172a; background: #fff; padding: 30px; line-height: 1.5; }
        .report-header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 3px solid #0f172a; }
        .report-header .company-name { font-size: 24px; font-weight: 700; letter-spacing: 2px; color: #0f172a; }
        .report-header .report-title { font-size: 16px; margin-top: 6px; font-weight: 600; color: #334155; }
        .report-header .report-date { font-size: 11px; margin-top: 4px; color: #64748b; }
        .summary-section { display: flex; gap: 16px; margin-bottom: 24px; }
        .summary-card { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; text-align: center; }
        .summary-card .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .summary-card .value { font-size: 20px; font-weight: 700; color: #0f172a; }
        .summary-card .value.text-primary { color: #4f46e5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; font-size: 11px; }
        th { background-color: #f1f5f9; font-weight: 600; color: #334155; text-transform: uppercase; font-size: 10px; letter-spacing: 0.3px; }
        td.text-right, th.text-right { text-align: right; }
        td.text-center, th.text-center { text-align: center; }
        tbody tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
        .signature-box { width: 220px; text-align: center; }
        .signature-box .line { border-top: 1px solid #0f172a; margin-top: 70px; padding-top: 6px; font-size: 11px; color: #334155; }
        .print-section { text-align: center; }
        .print-btn { display: inline-block; padding: 8px 20px; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; margin-top: 16px; }
        .print-btn:hover { background: #4338ca; }
        @media print { body { padding: 0; } @page { margin: 15mm; } .print-btn { display: none !important; } }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="company-name">SMART-CATALOG</div>
        <div class="report-title">Laporan Penjualan</div>
        <div class="report-date">
            @if(request('date_from') && request('date_to'))
                Periode: {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }} — {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
            @else
                Semua Data &middot; Dicetak: {{ now()->format('d/m/Y H:i') }}
            @endif
        </div>
    </div>

    <div class="summary-section">
        <div class="summary-card">
            <div class="label">Total Pendapatan</div>
            <div class="value text-primary">Rp {{ number_format($summary['total_revenue'] ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Qty</div>
            <div class="value">{{ number_format($summary['total_qty'] ?? 0) }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $summary['total_transactions'] ?? 0 }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>No. Transaksi</th>
                <th>Tanggal</th>
                <th>Merchant</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $sale)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale->transaction_number ?? '-' }}</td>
                    <td>{{ $sale->transaction_date ? $sale->transaction_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ $sale->merchant_code ?? '-' }}</td>
                    <td>{{ $sale->product?->product_name ?? '-' }}</td>
                    <td class="text-right">{{ $sale->qty }}</td>
                    <td class="text-right">Rp {{ number_format($sale->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($sale->subtotal ?? ($sale->qty * $sale->price), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <div class="line">Mengetahui,</div>
        </div>
    </div>

    <div class="print-section">
        <button class="print-btn" onclick="window.print();">Cetak Laporan</button>
    </div>
</body>
</html>
