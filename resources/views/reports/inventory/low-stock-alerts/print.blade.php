<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Low-stock Alerts</title>

        <style>
            :root {
                --text: #111827;
                --muted: #6b7280;
                --line: #e5e7eb;
                --soft: #f3f4f6;
                --brand: #111827;
                --danger: #b91c1c;
                --warn: #a16207;
            }

            * {
                box-sizing: border-box;
            }

            html,
            body {
                padding: 0;
                margin: 0;
                color: var(--text);
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI,
                    Roboto, Helvetica, Arial, "Apple Color Emoji",
                    "Segoe UI Emoji";
                background: #ffffff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                max-width: 1024px;
                margin: 0 auto;
                padding: 28px 28px 22px;
            }

            /* Header */
            .header {
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 16px;
                align-items: start;
                padding-bottom: 14px;
                border-bottom: 2px solid var(--text);
            }

            .title {
                margin: 0;
                font-size: 20px;
                line-height: 1.2;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .subtitle {
                margin: 6px 0 0 0;
                font-size: 12px;
                color: var(--muted);
                line-height: 1.5;
            }

            .meta {
                text-align: right;
                font-size: 12px;
                color: var(--muted);
                line-height: 1.5;
            }

            .meta strong {
                color: var(--text);
                font-weight: 600;
            }

            /* Summary */
            .summary {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
                margin-top: 14px;
            }

            .card {
                border: 1px solid var(--line);
                background: #fff;
                border-radius: 10px;
                padding: 12px 12px;
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
                font-size: 18px;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .card.alerts .value {
                color: var(--danger);
            }

            .card.shortage .value {
                color: var(--warn);
            }

            /* Table */
            .section {
                margin-top: 14px;
            }

            .section-title {
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: var(--text);
                margin: 0 0 8px 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid var(--line);
                border-radius: 10px;
                overflow: hidden;
            }

            thead th {
                background: var(--soft);
                border-bottom: 1px solid var(--line);
                font-size: 10px;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                text-align: left;
                padding: 10px 10px;
                color: var(--text);
                font-weight: 800;
                vertical-align: bottom;
                white-space: nowrap;
            }

            tbody td {
                border-top: 1px solid var(--line);
                padding: 10px 10px;
                font-size: 12px;
                vertical-align: top;
            }

            tbody tr:nth-child(even) td {
                background: #fafafa;
            }

            .right {
                text-align: right;
                white-space: nowrap;
            }

            .muted {
                color: var(--muted);
                font-size: 11px;
            }

            .pill {
                display: inline-block;
                border: 1px solid var(--line);
                background: #fff;
                border-radius: 999px;
                padding: 2px 8px;
                font-size: 10px;
                font-weight: 700;
                color: var(--text);
                white-space: nowrap;
            }

            .danger {
                color: var(--danger);
                font-weight: 800;
            }

            /* Footer */
            .footer {
                display: flex;
                justify-content: space-between;
                gap: 12px;
                border-top: 1px solid var(--line);
                margin-top: 14px;
                padding-top: 10px;
                color: var(--muted);
                font-size: 11px;
                line-height: 1.4;
            }

            /* Print adjustments */
            @page {
                size: A4;
                margin: 12mm;
            }

            @media print {
                .page {
                    max-width: none;
                    padding: 0;
                }

                a[href]:after {
                    content: "";
                }
            }
        </style>
    </head>
    <body>
        @php
            $generatedAt = now()->format('Y-m-d H:i');
            $typeLabel = function ($itemType) {
                if ($itemType === \App\Models\InventoryItem::class) return 'Inventory';
                if ($itemType === \App\Models\Medicine::class) return 'Medicine';
                return 'Item';
            };

            $number = function ($value) {
                $n = (float) ($value ?? 0);
                return number_format($n, (fmod($n, 1) === 0.0) ? 0 : 2);
            };

            $money = function ($value) {
                // Keep it generic: currency handled in app UI; print uses plain number formatting.
                $n = (float) ($value ?? 0);
                return number_format($n, 2);
            };
        @endphp

        <div class="page">
            <div class="header">
                <div>
                    <h1 class="title">Low-stock Alerts</h1>
                    <p class="subtitle">
                        Items below minimum stock level, calculated from stock
                        movements. Use this to prioritize replenishment.
                    </p>
                </div>

                <div class="meta">
                    <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                    <div>
                        <strong>Filters:</strong>
                        {{ ($filters['item_type'] ?? 'all') }}
                        @if(!empty($filters['q']))
                            • Search: “{{ $filters['q'] }}”
                        @endif
                        @if(($filters['only_below_min'] ?? true))
                            • Only below min
                        @endif
                    </div>
                    <div>
                        <strong>Sort:</strong>
                        {{ ($filters['sort'] ?? 'shortage') }}
                        ({{ ($filters['direction'] ?? 'desc') }})
                    </div>
                </div>
            </div>

            <div class="summary">
                <div class="card alerts">
                    <div class="label">Alerts</div>
                    <div class="value">{{ number_format((int)($summary['total_alerts'] ?? 0)) }}</div>
                </div>
                <div class="card shortage">
                    <div class="label">Total shortage</div>
                    <div class="value">{{ $number($summary['total_shortage'] ?? 0) }}</div>
                </div>
                <div class="card value">
                    <div class="label">Shortage value</div>
                    <div class="value">{{ $money($summary['total_shortage_value'] ?? 0) }}</div>
                    <div class="muted">Value at risk (estimated)</div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Alert details</h2>

                <table>
                    <thead>
                        <tr>
                            <th style="width: 38%">Item</th>
                            <th style="width: 10%">Type</th>
                            <th class="right" style="width: 10%">Min</th>
                            <th class="right" style="width: 10%">Current</th>
                            <th class="right" style="width: 10%">Shortage</th>
                            <th class="right" style="width: 11%">Avg unit cost</th>
                            <th class="right" style="width: 11%">Shortage value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rows ?? []) as $r)
                            <tr>
                                <td>
                                    <div style="font-weight: 800;">
                                        {{ $r['name'] ?? '—' }}
                                    </div>
                                    <div class="muted">
                                        @if(!empty($r['sku']))
                                            SKU: {{ $r['sku'] }}
                                        @else
                                            —
                                        @endif
                                        @if(!empty($r['unit']))
                                            • Unit: {{ $r['unit'] }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="pill">{{ $typeLabel($r['item_type'] ?? null) }}</span>
                                </td>
                                <td class="right">{{ $number($r['min_quantity'] ?? 0) }}</td>
                                <td class="right">{{ $number($r['current_stock'] ?? 0) }}</td>
                                <td class="right danger">{{ $number($r['shortage'] ?? 0) }}</td>
                                <td class="right">{{ $money($r['avg_unit_cost'] ?? 0) }}</td>
                                <td class="right">{{ $money($r['shortage_value'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; padding: 18px;" class="muted">
                                    No low-stock alerts for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <div>
                    <strong style="color: var(--text);">Note:</strong>
                    This report is generated from stock movement totals (In − Out + Adjustment).
                </div>
                <div style="text-align:right;">
                    <span>Printed from {{ config('app.name') }}</span>
                </div>
            </div>
        </div>

        <script>
            // Auto-open print dialog when the page is ready.
            window.addEventListener("load", () => {
                window.print();
            });
        </script>
    </body>
</html>
