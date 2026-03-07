<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, viewport-fit=cover"
        />
        <title>Wastage & Loss Report - Print</title>

        <style>
            /* Page */
            @page {
                size: A4;
                margin: 14mm;
            }

            :root {
                --text: #111827;
                --muted: #6b7280;
                --border: #e5e7eb;
                --bg: #ffffff;
                --header: #0f172a;
                --chip: #f3f4f6;
                --accent: #2563eb;
                --good: #047857;
                --warn: #b45309;
                --bad: #b91c1c;
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                height: 100%;
            }

            body {
                margin: 0;
                font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI",
                    Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
                color: var(--text);
                background: var(--bg);
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                max-width: 210mm;
                margin: 0 auto;
            }

            /* Header */
            .header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                padding-bottom: 12px;
                border-bottom: 2px solid var(--border);
            }

            .title {
                margin: 0;
                font-size: 18px;
                line-height: 1.2;
                font-weight: 800;
                letter-spacing: -0.01em;
            }

            .subtitle {
                margin-top: 6px;
                font-size: 12px;
                color: var(--muted);
                line-height: 1.35;
                max-width: 720px;
            }

            .meta {
                text-align: right;
                font-size: 12px;
                color: var(--muted);
                white-space: nowrap;
            }

            .meta strong {
                color: var(--text);
                font-weight: 700;
            }

            /* Chips */
            .chips {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 12px;
            }

            .chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                border: 1px solid var(--border);
                background: var(--chip);
                border-radius: 999px;
                font-size: 12px;
                color: var(--text);
            }

            .chip .label {
                color: var(--muted);
            }

            /* Summary */
            .summary {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
                margin-top: 14px;
            }

            .card {
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px 12px;
            }

            .card .kpi-label {
                font-size: 11px;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .card .kpi-value {
                margin-top: 6px;
                font-size: 18px;
                font-weight: 800;
            }

            .card .kpi-sub {
                margin-top: 4px;
                font-size: 11px;
                color: var(--muted);
            }

            /* Table */
            .table-wrap {
                margin-top: 16px;
                border: 1px solid var(--border);
                border-radius: 12px;
                overflow: hidden;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                font-size: 12px;
            }

            thead th {
                background: #f9fafb;
                color: #374151;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                font-size: 11px;
                text-align: left;
                padding: 10px 12px;
                border-bottom: 1px solid var(--border);
            }

            tbody td {
                padding: 10px 12px;
                border-bottom: 1px solid var(--border);
                vertical-align: top;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            tbody tr:last-child td {
                border-bottom: none;
            }

            .right {
                text-align: right;
                white-space: nowrap;
            }

            .muted {
                color: var(--muted);
                font-size: 11px;
                margin-top: 2px;
            }

            .pill {
                display: inline-flex;
                align-items: center;
                padding: 2px 8px;
                border-radius: 999px;
                border: 1px solid var(--border);
                background: #fff;
                font-size: 11px;
                color: #374151;
                white-space: nowrap;
            }

            .pct-good {
                color: var(--good);
                font-weight: 700;
            }
            .pct-warn {
                color: var(--warn);
                font-weight: 700;
            }
            .pct-bad {
                color: var(--bad);
                font-weight: 700;
            }

            .loss {
                color: var(--bad);
                font-weight: 700;
            }

            /* Footer */
            .footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 14px;
                padding-top: 10px;
                border-top: 1px solid var(--border);
                font-size: 11px;
                color: var(--muted);
            }

            /* Print helpers */
            .no-print {
                display: block;
                margin: 10px 0 14px;
                padding: 10px 12px;
                border: 1px dashed var(--border);
                border-radius: 10px;
                color: var(--muted);
                font-size: 12px;
            }

            @media print {
                .no-print {
                    display: none !important;
                }

                .table-wrap {
                    border-radius: 0;
                }

                thead {
                    display: table-header-group;
                }

                tfoot {
                    display: table-footer-group;
                }

                tr {
                    page-break-inside: avoid;
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="no-print">
                Tip: Press <strong>Ctrl/Cmd + P</strong> to print. For best
                results, enable <strong>Background graphics</strong>.
            </div>

            <header class="header">
                <div>
                    <h1 class="title">Wastage & Loss Report</h1>
                    <div class="subtitle">
                        Inventory losses (spoiled, expired, damaged, or wasted)
                        based on stock movements where
                        <strong>movement type</strong> is
                        <strong>loss</strong>.
                    </div>

                    <div class="chips">
                        <div class="chip">
                            <span class="label">Date range</span>
                            <span>
                                {{ !empty($filters['from'] ?? null) ? $filters['from'] : '—' }}
                                →
                                {{ !empty($filters['to'] ?? null) ? $filters['to'] : '—' }}
                            </span>
                        </div>

                        <div class="chip">
                            <span class="label">Item type</span>
                            <span style="text-transform: capitalize"
                                >{{ $filters['item_type'] ?? 'all' }}</span
                            >
                        </div>

                        @if (!empty($filters['q'] ?? null))
                            <div class="chip">
                                <span class="label">Search</span>
                                <span>{{ $filters['q'] }}</span>
                            </div>
                        @endif

                        <div class="chip">
                            <span class="label">Sort</span>
                            <span
                                >{{ $filters['sort'] ?? 'value' }}
                                ({{ $filters['direction'] ?? 'desc' }})</span
                            >
                        </div>
                    </div>
                </div>

                <div class="meta">
                    <div>
                        Generated:
                        <strong>
                            {{ \Illuminate\Support\Carbon::parse($generatedAt)->format('Y-m-d H:i') }}
                        </strong>
                    </div>
                </div>
            </header>

            <section class="summary">
                <div class="card">
                    <div class="kpi-label">Items with loss</div>
                    <div class="kpi-value">
                        {{ number_format((float) ($summary['total_items'] ?? 0)) }}
                    </div>
                </div>

                <div class="card">
                    <div class="kpi-label">Total loss quantity</div>
                    <div class="kpi-value">
                        {{ number_format((float) ($summary['total_loss_qty'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="card">
                    <div class="kpi-label">Total loss value</div>
                    <div class="kpi-value">
                        {{ number_format((float) ($summary['total_loss_value'] ?? 0), 2) }}
                    </div>
                    <div class="kpi-sub">Currency as per system settings</div>
                </div>

                <div class="card">
                    <div class="kpi-label">Avg wastage %</div>
                    <div class="kpi-value">
                        {{ number_format((float) ($summary['avg_wastage_pct'] ?? 0), 2) }}%
                    </div>
                    <div class="kpi-sub">
                        Loss qty ÷ Purchased qty (same period)
                    </div>
                </div>
            </section>

            <section class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 34%">Item</th>
                            <th style="width: 12%">Type</th>
                            <th style="width: 12%" class="right">Loss qty</th>
                            <th style="width: 10%" class="right">Unit</th>
                            <th style="width: 14%" class="right">Loss value</th>
                            <th style="width: 12%" class="right">
                                Purchased qty
                            </th>
                            <th style="width: 6%" class="right">%</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($rows as $r)
                            @php
                                $pct = (float) ($r['wastage_pct'] ?? 0);
                                $pctClass = $pct >= 10 ? 'pct-bad' : ($pct >= 5 ? 'pct-warn' : 'pct-good');
                            @endphp

                            <tr>
                                <td>
                                    <div style="font-weight: 800">
                                        {{ $r['name'] ?? '—' }}
                                    </div>
                                    <div class="muted">
                                        {{ !empty($r['sku'] ?? null) ? 'SKU: ' . $r['sku'] : '—' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="pill">
                                        @php($t = (string)($r['item_type'] ?? ''))
                                        {{ str_contains($t, 'InventoryItem') ? 'Inventory' : (str_contains($t, 'Medicine') ? 'Medicine' : 'Item') }}
                                    </span>
                                </td>

                                <td class="right loss">
                                    {{ number_format((float) ($r['loss_qty'] ?? 0), 2) }}
                                </td>

                                <td class="right">
                                    {{ $r['unit'] ?? '—' }}
                                </td>

                                <td class="right">
                                    {{ number_format((float) ($r['loss_value'] ?? 0), 2) }}
                                </td>

                                <td class="right">
                                    {{ number_format((float) ($r['purchased_qty'] ?? 0), 2) }}
                                </td>

                                <td class="right {{ $pctClass }}">
                                    {{ number_format($pct, 2) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 18px 12px">
                                    <div style="color: var(--muted)">
                                        No loss movements found for the selected
                                        filters.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <footer class="footer">
                <div>Report: <strong>Wastage & Loss</strong></div>
                <div>Powered by {{ config('app.name', 'Farm App') }}</div>
            </footer>
        </div>

        <script>
            // Auto-open the print dialog when this print page loads.
            window.addEventListener("load", () => {
                setTimeout(() => window.print(), 200);
            });
        </script>
    </body>
</html>
