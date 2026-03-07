<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Feed Consumed per Animal - Print</title>

    <style>
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
            --pos: #047857;
        }

        html, body { height: 100%; }

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
        .num { font-variant-numeric: tabular-nums; }
        .pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            background: #ecfeff;
            color: #155e75;
            border: 1px solid #cffafe;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        /* Layout */
        .sheet {
            max-width: 210mm;
            margin: 0 auto;
        }

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
            min-width: 180px;
        }
        .meta b { color: var(--ink); font-weight: 700; }

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

        tbody tr:last-child td { border-bottom: none; }

        .animal-tag { font-weight: 800; }
        .animal-name { margin-top: 2px; font-size: 10px; color: var(--muted); }

        .total { font-weight: 900; color: var(--ink); }
        .avg { font-weight: 800; color: var(--pos); }

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
                    <h1 class="title">Feed Consumed per Animal</h1>
                    <p class="subtitle">
                        Total feed issued to each animal from stock movements (feeding consumption).
                    </p>
                </div>
            </div>

            <div class="meta">
                <div><b>Printed:</b> {{ now()->format('d M Y, h:i A') }}</div>
                <div>
                    <b>Period:</b>
                    <span class="nowrap">
                        {{ !empty($filters['from']) ? \Illuminate\Support\Carbon::parse($filters['from'])->format('d M Y') : 'Start' }}
                        —
                        {{ !empty($filters['to']) ? \Illuminate\Support\Carbon::parse($filters['to'])->format('d M Y') : 'End' }}
                    </span>
                </div>
                @if(!empty($filters['animal_id']))
                    <div><b>Animal:</b> #{{ $filters['animal_id'] }}</div>
                @else
                    <div><b>Animal:</b> All</div>
                @endif
                @if(!empty($filters['q']))
                    <div><b>Search:</b> {{ $filters['q'] }}</div>
                @endif
                <div><b>Sort:</b> {{ $filters['sort'] ?? 'animal' }} ({{ $filters['direction'] ?? 'asc' }})</div>
            </div>
        </header>

        <section class="summary">
            <div class="card">
                <div class="label">Animals</div>
                <div class="value num">{{ number_format((float)($summary['total_animals'] ?? 0)) }}</div>
            </div>
            <div class="card">
                <div class="label">Total feed</div>
                <div class="value num">{{ number_format((float)($summary['total_feed'] ?? 0), 2) }}</div>
            </div>
            <div class="card">
                <div class="label">Avg daily feed (overall)</div>
                <div class="value num">{{ number_format((float)($summary['avg_daily_feed_overall'] ?? 0), 2) }}</div>
            </div>
        </section>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width: 46%;">Animal</th>
                        <th style="width: 18%;" class="text-right">Feeding days</th>
                        <th style="width: 18%;" class="text-right">Total feed</th>
                        <th style="width: 18%;" class="text-right">Avg daily feed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td>
                                <div class="animal-tag">{{ $r['tag'] ?? '—' }}</div>
                                <div class="animal-name">{{ $r['name'] ?? '—' }}</div>
                            </td>
                            <td class="text-right num">{{ number_format((float)($r['feeding_days'] ?? 0)) }}</td>
                            <td class="text-right num total">{{ number_format((float)($r['total_feed'] ?? 0), 2) }}</td>
                            <td class="text-right num avg">{{ number_format((float)($r['avg_daily_feed'] ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center muted" style="padding: 18px 8px;">
                                No results found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <footer class="footer">
            <div>
                <div class="watermark">INVENTORY REPORT</div>
                <div>Feed Consumed per Animal</div>
            </div>
            <div class="text-right">
                <div class="pill">System generated</div>
                <div style="margin-top: 4px;">Page 1 of 1</div>
            </div>
        </footer>
    </div>

    <script>
        window.addEventListener('load', () => {
            setTimeout(() => window.print(), 300);
        });
    </script>
</body>
</html>
