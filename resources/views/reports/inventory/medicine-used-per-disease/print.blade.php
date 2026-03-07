<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Medicine Used per Disease Report</title>

        <style>
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --brand: #111827; /* near-black */
                --accent: #2563eb; /* blue-600 */
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
                background: var(--brand);
                border-color: var(--brand);
                color: #fff;
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
                font-weight: 900;
                letter-spacing: -0.02em;
            }

            .brand .sub {
                margin-top: 4px;
                color: var(--muted);
                font-size: 12px;
                line-height: 1.4;
                max-width: 560px;
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
                font-weight: 700;
            }

            .cards {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
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
                font-weight: 800;
            }

            .card .v {
                margin-top: 6px;
                font-size: 18px;
                font-weight: 900;
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
                grid-column: span 6;
            }

            .filters .item strong {
                color: var(--ink);
                font-weight: 700;
            }

            .note {
                margin-top: 10px;
                border: 1px dashed var(--line);
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 12px;
                color: var(--muted);
                background: #fff;
            }

            .note strong {
                color: var(--ink);
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
                font-weight: 900;
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
                font-weight: 800;
                color: #374151;
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
                <button class="btn primary" type="button" onclick="window.print()">
                    Print
                </button>
            </div>

            <header class="topbar">
                <div class="brand">
                    <h1>Medicine Used per Disease</h1>
                    <div class="sub">
                        Total medicine quantity and cost used to treat each disease over a selected period.
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
                    <div class="k">Diseases</div>
                    <div class="v">{{ number_format((float)($summary['total_diseases'] ?? 0)) }}</div>
                </div>
                <div class="card">
                    <div class="k">Medicines</div>
                    <div class="v">{{ number_format((float)($summary['total_medicines'] ?? 0)) }}</div>
                </div>
                <div class="card">
                    <div class="k">Total quantity</div>
                    <div class="v">{{ number_format((float)($summary['total_quantity'] ?? 0), 2) }}</div>
                </div>
                <div class="card">
                    <div class="k">Total cost</div>
                    <div class="v">{{ number_format((float)($summary['total_cost'] ?? 0), 2) }}</div>
                </div>
            </section>

            <section class="filters">
                <div class="item">
                    <strong>From:</strong> {{ $filters['from'] ?? '—' }}
                </div>
                <div class="item">
                    <strong>To:</strong> {{ $filters['to'] ?? '—' }}
                </div>
                <div class="item">
                    <strong>Disease:</strong>
                    @php
                        $selectedDiseaseId = $filters['disease_id'] ?? null;
                        $selectedDiseaseName = 'All diseases';
                        if (!empty($selectedDiseaseId)) {
                            $match = collect($rows)->firstWhere('disease_id', (int) $selectedDiseaseId);
                            $selectedDiseaseName = $match['disease_name'] ?? ('#' . $selectedDiseaseId);
                        }
                    @endphp
                    {{ $selectedDiseaseName }}
                </div>
                <div class="item">
                    <strong>Search:</strong> {{ $filters['q'] ?? '—' }}
                </div>
                <div class="item">
                    <strong>Sort:</strong>
                    {{ $filters['sort'] ?? 'cost' }} ({{ $filters['direction'] ?? 'desc' }})
                </div>
            </section>

            @if(!empty($summary['note']))
                <div class="note">
                    <strong>Note:</strong> {{ $summary['note'] }}
                </div>
            @endif

            <section class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 34%">Disease</th>
                            <th style="width: 34%">Medicine</th>
                            <th class="right" style="width: 16%">Total quantity</th>
                            <th class="right" style="width: 16%">Total cost</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($rows as $r)
                            <tr>
                                <td>
                                    <div style="font-weight: 900">
                                        {{ $r['disease_name'] ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight: 900">
                                        {{ $r['medicine_name'] ?? '—' }}
                                    </div>
                                    <div class="muted" style="margin-top: 2px">
                                        @if(!empty($r['unit']))
                                            Unit: {{ $r['unit'] }}
                                        @else
                                            Unit: —
                                        @endif
                                    </div>
                                </td>

                                <td class="right" style="font-weight: 900">
                                    {{ number_format((float)($r['total_quantity'] ?? 0), 2) }}
                                </td>

                                <td class="right" style="font-weight: 900">
                                    {{ number_format((float)($r['total_cost'] ?? 0), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted" style="padding: 18px 12px">
                                    No data found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <footer class="footer">
                <div>
                    <strong>Report:</strong> Medicine Used per Disease
                </div>
                <div>
                    {{ config('app.name') }}
                </div>
            </footer>
        </div>

        <script>
            window.addEventListener("load", () => {
                window.print();
            });
        </script>
    </body>
</html>
