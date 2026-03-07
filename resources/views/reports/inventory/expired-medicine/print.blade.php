<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        />

        <title>Expired Medicine Report</title>

        <style>
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --danger: #b91c1c; /* red-700 */
                --warn: #b45309; /* amber-700 */
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
                padding: 24px;
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI,
                    Roboto, Helvetica, Arial, "Apple Color Emoji",
                    "Segoe UI Emoji";
                color: var(--ink);
                background: #ffffff;
            }

            .page {
                max-width: 980px;
                margin: 0 auto;
            }

            .topbar {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                padding-bottom: 14px;
                border-bottom: 2px solid var(--line);
            }

            .brand h1 {
                margin: 0;
                font-size: 20px;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .brand .sub {
                margin-top: 4px;
                color: var(--muted);
                font-size: 12px;
                line-height: 1.4;
                max-width: 520px;
            }

            .meta {
                text-align: right;
                font-size: 12px;
                color: var(--muted);
                line-height: 1.6;
                white-space: nowrap;
            }

            .meta strong {
                color: var(--ink);
                font-weight: 600;
            }

            .cards {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 12px;
                margin-top: 14px;
            }

            .card {
                border: 1px solid var(--line);
                border-radius: 10px;
                padding: 10px 12px;
                background: #fff;
            }

            .card .k {
                font-size: 11px;
                color: var(--muted);
                letter-spacing: 0.06em;
                text-transform: uppercase;
                font-weight: 700;
            }

            .card .v {
                margin-top: 6px;
                font-size: 18px;
                font-weight: 800;
            }

            .filters {
                margin-top: 14px;
                border: 1px solid var(--line);
                border-radius: 10px;
                background: var(--soft);
                padding: 10px 12px;
                display: grid;
                grid-template-columns: repeat(12, minmax(0, 1fr));
                gap: 10px;
                font-size: 12px;
                color: var(--muted);
            }

            .filters .item {
                grid-column: span 4;
            }

            .filters .item strong {
                color: var(--ink);
                font-weight: 600;
            }

            .table-wrap {
                margin-top: 14px;
                border: 1px solid var(--line);
                border-radius: 10px;
                overflow: hidden;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            thead th {
                background: var(--soft);
                color: #374151; /* gray-700 */
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                font-weight: 800;
                padding: 10px 12px;
                border-bottom: 1px solid var(--line);
                text-align: left;
            }

            tbody td {
                padding: 10px 12px;
                font-size: 12px;
                border-bottom: 1px solid var(--line);
                vertical-align: top;
            }

            tbody tr:last-child td {
                border-bottom: none;
            }

            .right {
                text-align: right;
            }

            .muted {
                color: var(--muted);
            }

            .pill {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 999px;
                background: #f3f4f6; /* gray-100 */
                border: 1px solid var(--line);
                font-size: 11px;
                font-weight: 700;
                color: #374151;
            }

            .expiry.expired {
                color: var(--danger);
                font-weight: 800;
            }

            .expiry.soon {
                color: var(--warn);
                font-weight: 800;
            }

            .footer {
                margin-top: 14px;
                padding-top: 12px;
                border-top: 1px solid var(--line);
                display: flex;
                justify-content: space-between;
                gap: 12px;
                font-size: 11px;
                color: var(--muted);
            }

            .actions {
                margin: 16px 0 0;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }

            .btn {
                appearance: none;
                border: 1px solid var(--line);
                background: #fff;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 12px;
                font-weight: 700;
                color: var(--ink);
                cursor: pointer;
            }

            .btn.primary {
                background: #111827;
                border-color: #111827;
                color: #fff;
            }

            @media print {
                body {
                    padding: 0;
                }

                .page {
                    max-width: none;
                    margin: 0;
                }

                .actions {
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

                tr,
                td,
                th {
                    break-inside: avoid;
                }

                @page {
                    size: A4;
                    margin: 12mm;
                }
            }
        </style>
    </head>

    <body>
        <div class="page">
            <div class="actions">
                <button class="btn" type="button" onclick="window.close()">
                    Close
                </button>
                <button
                    class="btn primary"
                    type="button"
                    onclick="window.print()"
                >
                    Print
                </button>
            </div>

            <header class="topbar">
                <div class="brand">
                    <h1>Expired Medicine Report</h1>
                    <div class="sub">
                        Batches with remaining stock that are expired or expiring
                        soon, calculated from stock movements.
                    </div>
                </div>

                <div class="meta">
                    <div>
                        <strong>Generated:</strong>
                        {{ optional($generatedAt)->format('Y-m-d H:i') }}
                    </div>
                    @if(!empty($farm))
                        <div><strong>Farm:</strong> {{ $farm->name ?? '—' }}</div>
                    @endif
                    <div><strong>By:</strong> {{ $user->name ?? '—' }}</div>
                </div>
            </header>

            <section class="cards">
                <div class="card">
                    <div class="k">Batches</div>
                    <div class="v">{{ number_format((float)($summary['total_batches'] ?? 0)) }}</div>
                </div>
                <div class="card">
                    <div class="k">Total stock</div>
                    <div class="v">{{ number_format((float)($summary['total_stock'] ?? 0), 2) }}</div>
                </div>
                <div class="card">
                    <div class="k">Stock value</div>
                    <div class="v">{{ number_format((float)($summary['total_value'] ?? 0), 2) }}</div>
                </div>
            </section>

            <section class="filters">
                <div class="item">
                    <strong>Status:</strong>
                    {{ ($filters['status'] ?? 'expired') === 'expiring_soon' ? 'Expiring soon' : 'Expired' }}
                </div>
                <div class="item">
                    <strong>Days:</strong> {{ (int)($filters['days'] ?? 30) }}
                </div>
                <div class="item">
                    <strong>Search:</strong> {{ $filters['q'] ?? '—' }}
                </div>
                <div class="item">
                    <strong>Sort:</strong>
                    {{ $filters['sort'] ?? 'expiry_date' }} ({{ $filters['direction'] ?? 'asc' }})
                </div>
            </section>

            <section class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 32%">Medicine</th>
                            <th style="width: 14%">Batch</th>
                            <th style="width: 14%">Expiry</th>
                            <th class="right" style="width: 12%">
                                Current stock
                            </th>
                            <th class="right" style="width: 14%">
                                Avg unit cost
                            </th>
                            <th class="right" style="width: 14%">
                                Stock value
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $today = now()->startOfDay();
                        @endphp

                        @forelse($rows as $r)
                            @php
                                $expiry = !empty($r['expiry_date']) ? \Illuminate\Support\Carbon::parse($r['expiry_date'])->startOfDay() : null;
                                $isExpired = $expiry ? $expiry->lt($today) : false;
                            @endphp

                            <tr>
                                <td>
                                    <div style="font-weight: 800">
                                        {{ $r['name'] ?? '—' }}
                                    </div>
                                    <div class="muted" style="margin-top: 2px">
                                        {{ !empty($r['sku']) ? 'SKU: ' . $r['sku'] : 'SKU: —' }}
                                        @if(!empty($r['unit']))
                                            &nbsp;&middot;&nbsp; Unit: {{ $r['unit'] }}
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <span class="pill">{{ $r['batch_no'] ?? '—' }}</span>
                                </td>

                                <td>
                                    <span class="expiry {{ $isExpired ? 'expired' : 'soon' }}">
                                        {{ $r['expiry_date'] ?? '—' }}
                                    </span>
                                </td>

                                <td class="right" style="font-weight: 800">
                                    {{ number_format((float)($r['current_stock'] ?? 0), 2) }}
                                </td>

                                <td class="right">
                                    {{ number_format((float)($r['avg_unit_cost'] ?? 0), 2) }}
                                </td>

                                <td class="right" style="font-weight: 800">
                                    {{ number_format((float)($r['stock_value'] ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="muted" style="padding: 18px 12px">
                                    No batches found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <footer class="footer">
                <div>
                    <strong>Note:</strong> This report shows batches with remaining
                    stock only.
                </div>
                <div>
                    {{ config('app.name') }}
                </div>
            </footer>
        </div>

        <script>
            // Optional: auto-open print dialog when page loads in a new tab.
            // Comment this out if you prefer manual click on "Print".
            window.addEventListener("load", () => {
                window.print();
            });
        </script>
    </body>
</html>
