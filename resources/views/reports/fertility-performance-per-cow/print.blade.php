<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Fertility Performance per Cow</title>

        <style>
            /* ---------- Base / screen ---------- */
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --brand: #111827;
                --accent: #2563eb; /* blue-600 */
                --ok: #047857; /* emerald-700 */
                --warn: #b45309; /* amber-700 */
                --bad: #be123c; /* rose-700 */
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
                grid-template-columns: 1.35fr 0.65fr;
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
                grid-template-columns: repeat(2, 1fr);
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
                white-space: nowrap;
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

            .pill-ok {
                background: #ecfdf5;
                border-color: #a7f3d0;
                color: var(--ok);
            }

            .pill-warn {
                background: #fffbeb;
                border-color: #fde68a;
                color: var(--warn);
            }

            .pill-bad {
                background: #fff1f2;
                border-color: #fecdd3;
                color: var(--bad);
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
            $serviceType = $filters['service_type'] ?? 'all';
            $serviceLabel = match ($serviceType) {
                'ai' => 'Artificial Insemination (AI)',
                'natural_mating' => 'Natural Mating',
                'embryo_transfer' => 'Embryo Transfer',
                default => 'All services',
            };

            $performance = $filters['performance'] ?? 'all';
            $performanceLabel = match ($performance) {
                'excellent' => 'Excellent',
                'moderate' => 'Moderate',
                'poor' => 'Poor',
                default => 'All',
            };

            $animalLabel = 'All animals';
            if (!empty($filters['animal_id'])) {
                $a = collect($animals)->firstWhere('id', (int) $filters['animal_id']);
                if ($a) {
                    $animalLabel = trim(($a['tag'] ?? '—') . (!empty($a['name']) ? ' - ' . $a['name'] : ''));
                } else {
                    $animalLabel = 'Selected animal';
                }
            }

            $formatNullable = function ($v) {
                if ($v === null || $v === '' || $v === false) {
                    return '—';
                }
                return $v;
            };

            $pillClass = function ($bucket) {
                if ($bucket === 'excellent') {
                    return 'pill-ok';
                }
                if ($bucket === 'moderate') {
                    return 'pill-warn';
                }
                return 'pill-bad';
            };
        @endphp

        <div class="page">
            <div class="header">
                <div class="header-top">
                    <div class="header-title">
                        <h1>Fertility Performance per Cow</h1>
                        <div class="meta">Generated: {{ $generatedAt }}</div>
                    </div>
                </div>

                <div class="header-bottom">
                    <div class="kv">
                        <div class="k">Date range</div>
                        <div class="v">
                            {{ $summary['from'] ?? '' }} → {{ $summary['to'] ?? '' }}
                        </div>

                        <div class="k">Service type</div>
                        <div class="v">{{ $serviceLabel }}</div>

                        <div class="k">Performance</div>
                        <div class="v">{{ $performanceLabel }}</div>

                        <div class="k">Animal</div>
                        <div class="v">{{ $animalLabel }}</div>
                    </div>

                    <div class="stats">
                        <div class="stat">
                            <div class="k">Cows</div>
                            <div class="v">{{ $summary['cows'] ?? 0 }}</div>
                        </div>
                        <div class="stat">
                            <div class="k">Avg Conception</div>
                            <div class="v">{{ $summary['avg_conception_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="stat">
                            <div class="k">Avg SPC</div>
                            <div class="v">{{ $formatNullable($summary['avg_spc'] ?? null) }}</div>
                        </div>
                        <div class="stat">
                            <div class="k">Avg Days Open</div>
                            <div class="v">{{ $formatNullable($summary['avg_days_open'] ?? null) }}</div>
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

            <div class="section">
                <div class="section-h">
                    <h2>Results</h2>
                    <div class="note">Showing {{ count($rows ?? []) }} cow(s)</div>
                </div>

                <div style="overflow: hidden">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 140px">Cow</th>
                                <th class="num" style="width: 82px">Services</th>
                                <th class="num" style="width: 98px">Pregnancies</th>
                                <th class="num" style="width: 120px">Conception</th>
                                <th class="num" style="width: 70px">SPC</th>
                                <th class="num" style="width: 92px">Days open</th>
                                <th class="num" style="width: 118px">Calving int.</th>
                                <th class="num" style="width: 92px">Loss rate</th>
                                <th class="num" style="width: 72px">Score</th>
                                <th style="width: 110px">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700">
                                            {{ $r['tag_number'] ?? '—' }}
                                        </div>
                                        <div class="muted" style="font-size: 11px">
                                            {{ $r['animal_name'] ?? 'Unnamed' }}
                                        </div>
                                    </td>

                                    <td class="num">{{ $r['total_services'] ?? 0 }}</td>
                                    <td class="num">{{ $r['confirmed_pregnancies'] ?? 0 }}</td>
                                    <td class="num">{{ $r['conception_rate'] ?? 0 }}%</td>
                                    <td class="num">{{ $formatNullable($r['spc'] ?? null) }}</td>
                                    <td class="num">{{ $formatNullable($r['avg_days_open'] ?? null) }}</td>
                                    <td class="num">{{ $formatNullable($r['avg_calving_interval_days'] ?? null) }}</td>
                                    <td class="num">{{ $r['pregnancy_loss_rate'] ?? 0 }}%</td>
                                    <td class="num" style="font-weight: 700">{{ $r['fertility_score'] ?? 0 }}</td>
                                    <td>
                                        <span class="pill {{ $pillClass($r['performance'] ?? 'poor') }}">
                                            {{ $r['performance_label'] ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="padding: 18px; text-align: center" class="muted">
                                        No cows found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer">
                <div>Fertility Performance per Cow</div>
                <div>Generated: {{ $generatedAt }}</div>
            </div>
        </div>

        <script>
            // Auto-open print dialog for a smoother "Print" experience.
            // Small delay lets fonts/layout settle.
            window.addEventListener("load", () => {
                setTimeout(() => window.print(), 350);
            });
        </script>
    </body>
</html>
