<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Current Stock by Item - Print</title>

    <style>
        /* Page + typography */
        @page {
            size: A4;
            margin: 14mm 12mm;
        }

        :root{
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --soft: #f3f4f6;
            --brand: #111827;
            --accent: #2563eb;
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color: var(--ink);
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Utility */
        .muted { color: var(--muted); }
        .nowrap { white-space: nowrap; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Layout */
        .sheet {
            max-width: 210mm;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--brand);
            margin-bottom: 12px;
        }

        .brand {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand), #374151);
            display: inline-block;
        }

        .title {
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .subtitle {
            margin: 3px 0 0;
            font-size: 12px;
            line-height: 1.35;
            color: var(--muted);
        }

        .meta {
            text-align: right;
            font-size: 11px;
            line-height: 1.45;
            color: var(--muted);
            min-width: 170px;
        }

        .meta b { color: var(--ink); font-weight: 700; }

        /* Summary */
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin: 12px 0 10px;
        }

        .card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 10px 9px;
            background: #fff;
        }

        .card .label {
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 700;
        }

        .card .value {
            margin-top: 6px;
            font-size: 16px;
            font-weight: 800;
        }

        /* Table */
        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead th {
            background: var(--soft);
            color: #374151;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 800;
            padding: 8px 8px;
            border-bottom: 1px solid var(--line);
        }

        tbody td {
            font-size: 11px;
            padding: 7px 8px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            font-weight: 700;
            color: var(--ink);
        }
        .item-sub {
            margin-top: 2px;
            font-size: 10px;
            color: var(--muted);
        }

        .pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            border: 1px solid #e0e7ff;
            font-size: 10px;
            font-weight: 700;
        }

        .num {
            font-variant-numeric: tabular-nums;
        }

        .neg { color: #b91c1c; font-weight: 700; }
        .pos { color: #047857; font-weight: 700; }
        .zero { color: var(--ink); font-weight: 700; }

        /* Footer */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            border-top: 1px solid var(--line);
            margin-top: 12px;
            padding-top: 10px;
            font-size: 10px;
            color: var(--muted);
        }

        .watermark {
            font-weight: 800;
            color: #111827;
            opacity: 0.5;
            letter-spacing: 0.08em;
        }

        /* Print controls (hidden in print) */
        .actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin: 0 0 10px;
        }

        .btn {
            appearance: none;
            border: 1px solid #111827;
            background: #111827;
            color: #fff;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn.secondary {
            background: #fff;
            color: #111827;
        }

        @media print {
            .actions { display: none !important; }
            a[href]:after { content: ""; }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="actions">
            <button class="btn secondary" type="button" onclick="window.close()">Close</button>
            <button class="btn" type="button" onclick="window.print()">Print</button>
        </div>

        <header class="topbar">
            <div class="brand">
                <span class="logo" aria-hidden="true"></span>
                <div>
                    <h1 class="title">Current Stock by Item</h1>
                    <p class="subtitle">
                        Inventory snapshot based on stock movements.
                    </p>
                </div>
            </div>

            <div class="meta">
                <div><b>Printed:</b> {{ now()->format('d M Y, h:i A') }}</div>
                <div><b>Filters:</b>
                    <span class="nowrap">
                        Type: {{ $filters['item_type'] ?? 'all' }},
                        Only in stock: {{ !empty($filters['only_in_stock']) ? 'yes' : 'no' }}
                    </span>
                </div>
                @if(!empty($filters['q']))
                    <div><b>Search:</b> {{ $filters['q'] }}</div>
                @endif
                <div><b>Sort:</b> {{ $filters['sort'] ?? 'name' }} ({{ $filters['direction'] ?? 'asc' }})</div>
            </div>
        </header>

        <section class="summary">
            <div class="card">
                <div class="label">Total items</div>
                <div class="value num">{{ number_format((float)($summary['total_items'] ?? 0)) }}</div>
            </div>
            <div class="card">
                <div class="label">Total stock</div>
                <div class="value num">{{ number_format((float)($summary['total_stock'] ?? 0), 2) }}</div>
            </div>
            <div class="card">
                <div class="label">Total value</div>
                <div class="value num">{{ number_format((float)($summary['total_value'] ?? 0), 2) }}</div>
            </div>
        </section>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 32%;">Item</th>
                        <th style="width: 10%;">Type</th>
                        <th style="width: 8%;" class="text-right">In</th>
                        <th style="width: 8%;" class="text-right">Out</th>
                        <th style="width: 8%;" class="text-right">Adj</th>
                        <th style="width: 10%;" class="text-right">Current</th>
                        <th style="width: 12%;" class="text-right">Avg unit</th>
                        <th style="width: 12%;" class="text-right">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        @php
                            $current = (float)($r['current_stock'] ?? 0);
                            $currentClass = $current > 0 ? 'pos' : ($current < 0 ? 'neg' : 'zero');
                            $type = $r['item_type'] ?? '';
                            $typeLabel = $type === 'App\\\\Models\\\\InventoryItem' ? 'Inventory' : ($type === 'App\\\\Models\\\\Medicine' ? 'Medicine' : 'Item');
                        @endphp
                        <tr>
                            <td>
                                <div class="item-name">{{ $r['name'] ?? '—' }}</div>
                                <div class="item-sub">
                                    {{ !empty($r['sku']) ? 'SKU: '.$r['sku'] : '—' }}
                                    @if(!empty($r['unit']))
                                        • Unit: {{ $r['unit'] }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="pill">{{ $typeLabel }}</span>
                            </td>
                            <td class="text-right num">{{ number_format((float)($r['total_in'] ?? 0), 2) }}</td>
                            <td class="text-right num">{{ number_format((float)($r['total_out'] ?? 0), 2) }}</td>
                            <td class="text-right num">{{ number_format((float)($r['total_adjustment'] ?? 0), 2) }}</td>
                            <td class="text-right num {{ $currentClass }}">{{ number_format($current, 2) }}</td>
                            <td class="text-right num">{{ number_format((float)($r['avg_unit_cost'] ?? 0), 2) }}</td>
                            <td class="text-right num">{{ number_format((float)($r['stock_value'] ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center muted" style="padding: 18px 8px;">
                                No items found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="footer">
            <div>
                <div class="watermark">INVENTORY REPORT</div>
                <div>Current Stock by Item</div>
            </div>
            <div class="text-right">
                <div>System generated report.</div>
                <div>Page 1 of 1</div>
            </div>
        </footer>
    </div>

    <script>
        // Auto-open print dialog on load (with a small delay to ensure layout is ready)
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 300);
        });

        // Optional: if user prints and closes tab behavior is desired, uncomment:
        // window.addEventListener('afterprint', () => window.close());
    </script>
</body>
</html>
