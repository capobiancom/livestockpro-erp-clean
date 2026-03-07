<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Calving Interval Report</title>

        <style>
            /* ---------- Base / screen ---------- */
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --brand: #111827;
                --good: #b45309;
                --good-bg: #fffbeb;
                --good-bd: #fde68a;
                --exc: #047857;
                --exc-bg: #ecfdf5;
                --exc-bd: #a7f3d0;
                --poor: #b91c1c;
                --poor-bg: #fef2f2;
                --poor-bd: #fecaca;
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
                font-family:
                    ui-sans-serif,
                    system-ui,
                    -apple-system,
                    Segoe UI,
                    Roboto,
                    Helvetica,
                    Arial,
                    "Apple Color Emoji",
                    "Segoe UI Emoji";
                color: var(--ink);
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page {
                max-width: 980px;
                margin: 24px auto;
                padding: 0 24px;
            }

            .toolbar {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                margin: 16px 0 0;
            }

            .btn {
                appearance: none;
                border: 1px solid var(--line);
                background: #fff;
                color: var(--ink);
                padding: 10px 12px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
            }

            .btn-primary {
                background: var(--brand);
                color: #fff;
                border-color: var(--brand);
            }

            .header {
                border: 1px solid var(--line);
                border-radius: 14px;
                overflow: hidden;
            }

            .header-top {
                background: linear-gradient(
                    135deg,
                    rgba(17, 24, 39, 1) 0%,
                    rgba(31, 41, 55, 1) 100%
                );
                color: #fff;
                padding: 18px 18px 16px;
            }

            .header-title {
                display: flex;
                align-items: baseline;
                justify-content: space-between;
                gap: 12px;
            }

            .header-title h1 {
                margin: 0;
                font-size: 18px;
                letter-spacing: 0.2px;
            }

            .header-title .meta {
                font-size: 11px;
                opacity: 0.9;
                white-space: nowrap;
            }

            .header-bottom {
                padding: 14px 18px;
                background: #fff;
                display: grid;
                grid-template-columns: 1.2fr 0.8fr;
                gap: 12px;
                border-top: 1px solid rgba(255, 255, 255, 0.15);
            }

            .kv {
                display: grid;
                grid-template-columns: 140px 1fr;
                row-gap: 6px;
                column-gap: 10px;
                font-size: 12px;
                color: var(--ink);
            }

            .kv .k {
                color: var(--muted);
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
            }

            .stat {
                border: 1px solid var(--line);
                border-radius: 12px;
                padding: 10px 12px;
                background: var(--soft);
            }

            .stat .k {
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
            }

            .stat .v {
                margin-top: 6px;
                font-size: 16px;
                font-weight: 700;
            }

            .section {
                margin-top: 14px;
                border: 1px solid var(--line);
                border-radius: 14px;
                overflow: hidden;
            }

            .section-h {
                padding: 12px 16px;
                background: #fff;
                border-bottom: 1px solid var(--line);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .section-h h2 {
                margin: 0;
                font-size: 13px;
                font-weight: 700;
            }

            .section-h .note {
                font-size: 11px;
                color: var(--muted);
                white-space: nowrap;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            thead th {
                text-align: left;
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--muted);
                background: var(--soft);
                border-bottom: 1px solid var(--line);
                padding: 10px 12px;
            }

            tbody td {
                font-size: 12px;
                padding: 10px 12px;
                border-bottom: 1px solid var(--line);
                vertical-align: top;
            }

            tbody tr:last-child td {
                border-bottom: 0;
            }

            .num {
                text-align: right;
                white-space: nowrap;
                font-variant-numeric: tabular-nums;
            }

            .pill {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 4px 8px;
                font-size: 10px;
                font-weight: 700;
                border: 1px solid var(--line);
                background: #fff;
                white-space: nowrap;
            }

            .pill-excellent {
                background: var(--exc-bg);
                border-color: var(--exc-bd);
                color: var(--exc);
            }

            .pill-good {
                background: var(--good-bg);
                border-color: var(--good-bd);
                color: var(--good);
            }

            .pill-poor {
                background: var(--poor-bg);
                border-color: var(--poor-bd);
                color: var(--poor);
            }

            .muted {
                color: var(--muted);
            }

            .footer {
                margin-top: 14px;
                font-size: 11px;
                color: var(--muted);
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }

            /* ---------- Print ---------- */
            @page {
                size: A4;
                margin: 10mm;
            }

            @media print {
                .page {
                    max-width: none;
                    margin: 0;
                    padding: 0;
                }

                .toolbar {
                    display: none !important;
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
                    page-break-inside: avoid;
                }

                .section,
                .header {
                    border-radius: 0;
                }
            }
        </style>
    </head>

    <body>
        @php
            $animalLabel = 'All animals';
            if (!empty($filters['animal_id'])) {
                $a = collect($animals ?? [])->firstWhere('id', (int) $filters['animal_id']);
                if ($a) {
                    $animalLabel = trim(($a['name'] ?? 'Unnamed') . (!empty($a['tag']) ? ' (' . $a['tag'] . ')' : ''));
                } else {
                    $animalLabel = 'Selected animal';
                }
            }

            $perfLabel = 'All';
            $perf = $filters['performance'] ?? 'all';
            if ($perf === 'excellent') {
                $perfLabel = 'Excellent (≤ 365 days)';
            } elseif ($perf === 'good') {
                $perfLabel = 'Good (366–420 days)';
            } elseif ($perf === 'poor') {
                $perfLabel = 'Poor (> 420 days)';
            }
        @endphp

        <div class="page">
            <div class="header">
                <div class="header-top">
                    <div class="header-title">
                        <h1>Calving Interval Report</h1>
                        <div class="meta">Generated: {{ $generatedAt }}</div>
                    </div>
                </div>

                <div class="header-bottom">
                    <div class="kv">
                        <div class="k">Date range</div>
                        <div class="v">{{ $summary['from'] ?? '—' }} → {{ $summary['to'] ?? '—' }}</div>

                        <div class="k">Animal</div>
                        <div class="v">{{ $animalLabel }}</div>

                        <div class="k">Performance filter</div>
                        <div class="v">{{ $perfLabel }}</div>

                        <div class="k">Definition</div>
                        <div class="v muted">Current Calving Date − Previous Calving Date</div>
                    </div>

                    <div>
                        <div class="stats">
                            <div class="stat">
                                <div class="k">Total intervals</div>
                                <div class="v">{{ $summary['total_intervals'] ?? 0 }}</div>
                            </div>
                            <div class="stat">
                                <div class="k">Average CI</div>
                                <div class="v">{{ $summary['average_ci_days'] ?? 0 }}</div>
                            </div>
                            <div class="stat">
                                <div class="k">Min CI</div>
                                <div class="v">{{ $summary['min_ci_days'] ?? 0 }}</div>
                            </div>
                            <div class="stat">
                                <div class="k">Max CI</div>
                                <div class="v">{{ $summary['max_ci_days'] ?? 0 }}</div>
                            </div>
                        </div>

                        <div class="toolbar">
                            <button class="btn" type="button" onclick="window.close()">
                                Close
                            </button>
                            <button class="btn btn-primary" type="button" onclick="window.print()">
                                Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-h">
                    <h2>Results</h2>
                    <div class="note">Showing {{ count($rows ?? []) }} interval(s)</div>
                </div>

                <div style="overflow: hidden">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 170px">Cow</th>
                                <th style="width: 120px">Previous calving</th>
                                <th style="width: 120px">Current calving</th>
                                <th style="width: 90px; text-align: right">CI (days)</th>
                                <th style="width: 160px">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                @php
                                    $pill = 'pill-good';
                                    if (($r['performance'] ?? '') === 'excellent') {
                                        $pill = 'pill-excellent';
                                    } elseif (($r['performance'] ?? '') === 'poor') {
                                        $pill = 'pill-poor';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight: 700">{{ $r['tag'] ?? ($r['tag_number'] ?? '—') }}</div>
                                        @if (!empty($r['animal_name']))
                                            <div class="muted" style="font-size: 11px">{{ $r['animal_name'] }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $r['previous_calving_date'] ?? '—' }}</td>
                                    <td>{{ $r['current_calving_date'] ?? '—' }}</td>
                                    <td class="num">{{ $r['calving_interval_days'] ?? 0 }}</td>
                                    <td>
                                        <span class="pill {{ $pill }}">
                                            {{ $r['performance_label'] ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="padding: 18px; text-align: center" class="muted">
                                        No calving intervals found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer">
                <div>Calving Interval Report</div>
                <div>Generated: {{ $generatedAt }}</div>
            </div>
        </div>

        <script>
            // Auto-open print dialog for a smoother "Print" experience.
            window.addEventListener("load", () => {
                setTimeout(() => window.print(), 350);
            });
        </script>
    </body>
</html>
