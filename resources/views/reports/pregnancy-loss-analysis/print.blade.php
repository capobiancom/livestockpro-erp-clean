<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, viewport-fit=cover"
        />
        <title>Pregnancy Loss Analysis - Print</title>

        <style>
            /* ---- Page setup ---- */
            @page {
                size: A4;
                margin: 14mm 12mm;
            }

            :root {
                --text: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --border: #e5e7eb; /* gray-200 */
                --bg-soft: #f9fafb; /* gray-50 */
                --accent: #111827; /* gray-900 */
                --danger: #be123c; /* rose-700 */
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
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI,
                    Roboto, Helvetica, Arial, "Apple Color Emoji",
                    "Segoe UI Emoji";
                color: var(--text);
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            /* ---- Layout ---- */
            .page {
                width: 100%;
            }

            .topbar {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                padding-bottom: 10px;
                border-bottom: 1px solid var(--border);
            }

            .brand {
                display: flex;
                gap: 10px;
                align-items: flex-start;
            }

            .logo {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                background: var(--accent);
                flex: none;
            }

            .titles h1 {
                margin: 0;
                font-size: 16px;
                font-weight: 700;
                letter-spacing: 0.2px;
            }

            .titles .sub {
                margin-top: 2px;
                font-size: 11px;
                color: var(--muted);
                line-height: 1.35;
            }

            .meta {
                text-align: right;
                flex: none;
                min-width: 220px;
            }

            .meta .line {
                font-size: 11px;
                color: var(--muted);
                line-height: 1.4;
            }

            .meta .value {
                color: var(--text);
                font-weight: 600;
            }

            .section {
                margin-top: 12px;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(12, 1fr);
                gap: 10px;
            }

            .card {
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 10px;
                background: white;
            }

            .card.soft {
                background: var(--bg-soft);
            }

            .kpi-label {
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: var(--muted);
                font-weight: 700;
            }

            .kpi-value {
                margin-top: 6px;
                font-size: 14px;
                font-weight: 700;
            }

            .kpi-sub {
                margin-top: 4px;
                font-size: 10px;
                color: var(--muted);
                line-height: 1.35;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 8px;
                border-radius: 999px;
                border: 1px solid var(--border);
                font-size: 10px;
                font-weight: 700;
                color: var(--text);
                background: white;
                white-space: nowrap;
            }

            .badge.good {
                border-color: #a7f3d0; /* emerald-200 */
                background: #ecfdf5; /* emerald-50 */
                color: #047857; /* emerald-700 */
            }

            .badge.bad {
                border-color: #fecdd3; /* rose-200 */
                background: #fff1f2; /* rose-50 */
                color: #be123c; /* rose-700 */
            }

            h2 {
                margin: 0 0 8px 0;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.2px;
            }

            .table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
                overflow: hidden;
                border: 1px solid var(--border);
                border-radius: 12px;
            }

            .table thead th {
                background: var(--bg-soft);
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #4b5563; /* gray-600 */
                font-weight: 800;
                padding: 8px 10px;
                border-bottom: 1px solid var(--border);
                text-align: left;
                vertical-align: bottom;
            }

            .table tbody td {
                padding: 8px 10px;
                border-bottom: 1px solid var(--border);
                font-size: 11px;
                vertical-align: top;
            }

            .table tbody tr:last-child td {
                border-bottom: 0;
            }

            .table .right {
                text-align: right;
                white-space: nowrap;
            }

            .muted {
                color: var(--muted);
            }

            .footer {
                margin-top: 12px;
                padding-top: 8px;
                border-top: 1px solid var(--border);
                display: flex;
                justify-content: space-between;
                gap: 10px;
                font-size: 10px;
                color: var(--muted);
            }

            /* Keep header/footer visible on print */
            .no-print {
                display: none;
            }

            /* Avoid breaking cards/tables awkwardly */
            .card,
            .table,
            .topbar {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            /* On-screen helpers (optional) */
            @media screen {
                body {
                    background: #f3f4f6;
                }
                .page {
                    max-width: 860px;
                    margin: 18px auto;
                    background: white;
                    border: 1px solid var(--border);
                    border-radius: 14px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
                    padding: 18px;
                }
                .no-print {
                    display: block;
                    margin-bottom: 12px;
                }
                .toolbar {
                    display: flex;
                    justify-content: flex-end;
                    gap: 8px;
                }
                .btn {
                    border: 1px solid var(--border);
                    background: white;
                    border-radius: 10px;
                    padding: 8px 12px;
                    font-size: 12px;
                    font-weight: 700;
                    cursor: pointer;
                }
                .btn.primary {
                    background: var(--accent);
                    color: white;
                    border-color: var(--accent);
                }
            }
        </style>
    </head>

    <body>
        <div class="page">
            <div class="no-print">
                <div class="toolbar">
                    <button class="btn" onclick="window.close()">Close</button>
                    <button class="btn primary" onclick="window.print()">
                        Print
                    </button>
                </div>
            </div>

            <header class="topbar">
                <div class="brand">
                    <div class="logo" aria-hidden="true"></div>
                    <div class="titles">
                        <h1>Pregnancy Loss Analysis</h1>
                        <div class="sub">
                            Measure the rate and timing of pregnancies that were
                            confirmed but later lost.
                        </div>
                    </div>
                </div>

                <div class="meta">
                    <div class="line">
                        Date range:
                        <span class="value"
                            >{{ $summary['from'] }} → {{ $summary['to'] }}</span
                        >
                    </div>
                    <div class="line">
                        Generated:
                        <span class="value">{{ $generated_at }}</span>
                    </div>
                    <div class="line">
                        Farm:
                        <span class="value"
                            >{{ $farm?->name ?? '—' }}</span
                        >
                    </div>
                </div>
            </header>

            <section class="section">
                <div class="grid">
                    <div class="card soft" style="grid-column: span 3">
                        <div class="kpi-label">Confirmed pregnancies</div>
                        <div class="kpi-value">
                            {{ $summary['total_confirmed_pregnancies'] ?? 0 }}
                        </div>
                    </div>

                    <div class="card soft" style="grid-column: span 3">
                        <div class="kpi-label">Pregnancy losses</div>
                        <div class="kpi-value">
                            {{ $summary['pregnancy_losses'] ?? 0 }}
                        </div>
                        <div class="kpi-sub">
                            Abortion:
                            {{ $summary['loss_type_counts']['abortion'] ?? 0 }},
                            Embryonic death:
                            {{
                                $summary['loss_type_counts']['embryonic_death'] ??
                                    0
                            }},
                            Miscarriage:
                            {{
                                $summary['loss_type_counts']['miscarriage'] ??
                                    0
                            }}
                        </div>
                    </div>

                    @php
                        $rate = (float) ($summary['pregnancy_loss_rate'] ?? 0);
                        $badgeClass = $rate <= 15 ? 'good' : 'bad';
                        $badgeText = $rate <= 15 ? 'Within benchmark (≤ 15%)' : 'Above benchmark (> 15%)';
                    @endphp

                    <div class="card" style="grid-column: span 6">
                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                gap: 10px;
                                align-items: baseline;
                            "
                        >
                            <div>
                                <div class="kpi-label">
                                    Pregnancy loss rate
                                </div>
                                <div class="kpi-value">{{ $rate }}%</div>
                                <div class="kpi-sub">
                                    Formula: (Pregnancy Losses ÷ Total Confirmed
                                    Pregnancies) × 100
                                </div>
                            </div>
                            <div class="badge {{ $badgeClass }}">
                                {{ $badgeText }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>Results summary</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                @if (($filters['group_by'] ?? 'loss_timing') === 'month')
                                    Month (confirmed)
                                @else
                                    Loss timing
                                @endif
                            </th>
                            <th class="right">Confirmed</th>
                            <th class="right">Losses</th>
                            <th class="right">Loss rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupBy = $filters['group_by'] ?? 'loss_timing';
                            $totalConfirmed = (int) ($summary['total_confirmed_pregnancies'] ?? 0);
                        @endphp

                        @forelse ($rows as $r)
                            @php
                                $group = $r['group'] ?? '—';

                                if ($groupBy === 'month') {
                                    $confirmed = (int) ($r['confirmed'] ?? 0);
                                    $losses = (int) ($r['losses'] ?? 0);
                                    $rowRate = (float) ($r['rate'] ?? 0);
                                } else {
                                    // loss_timing: backend returns losses only; show context using total confirmed
                                    $confirmed = $totalConfirmed;
                                    $losses = (int) ($r['losses'] ?? 0);
                                    $rowRate = $confirmed > 0 ? round(($losses / $confirmed) * 100, 2) : 0;
                                }
                            @endphp
                            <tr>
                                <td>{{ $group }}</td>
                                <td class="right">{{ $confirmed }}</td>
                                <td class="right">{{ $losses }}</td>
                                <td class="right">{{ $rowRate }}%</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="muted">
                                    No confirmed pregnancies found for the
                                    selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="section">
                <h2>Pregnancy details</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pregnancy ID</th>
                            <th>Animal</th>
                            <th>Confirmed</th>
                            <th>Loss type</th>
                            <th>Loss date</th>
                            <th class="right">Days from confirm</th>
                            <th>Timing bucket</th>
                            <th>Calving date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($details as $d)
                            @php
                                $animalText = trim(
                                    collect([
                                        $d['animal_tag'] ?? null,
                                        $d['animal_name'] ?? null,
                                    ])
                                        ->filter()
                                        ->implode(' - ')
                                );

                                $lossTypeMap = [
                                    'abortion' => 'Abortion',
                                    'embryonic_death' => 'Embryonic death',
                                    'miscarriage' => 'Miscarriage',
                                    'unknown' => 'Unknown',
                                    'aborted' => 'Abortion',
                                ];

                                $timingMap = [
                                    'early_embryonic' => 'Early embryonic (< 42 days)',
                                    'mid_gestation' => 'Mid-gestation (42–179 days)',
                                    'late_abortion' => 'Late abortion (≥ 180 days)',
                                ];
                            @endphp
                            <tr>
                                <td>{{ $d['pregnancy_id'] ?? '—' }}</td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 2px">
                                        <div style="font-weight: 700">
                                            {{
                                                $animalText !== ''
                                                    ? $animalText
                                                    : ($d['animal_id'] ?? '—')
                                            }}
                                        </div>
                                        <div class="muted" style="font-size: 10px">
                                            ID: {{ $d['animal_id'] ?? '—' }}
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $d['confirmed_date'] ?? '—' }}</td>
                                <td>
                                    {{
                                        $lossTypeMap[$d['loss_type'] ?? 'unknown'] ??
                                            'Unknown'
                                    }}
                                </td>
                                <td>{{ $d['loss_date'] ?? '—' }}</td>
                                <td class="right">
                                    {{ $d['days_from_confirm'] ?? '—' }}
                                </td>
                                <td>
                                    {{
                                        $timingMap[$d['loss_timing'] ?? ''] ??
                                            ($d['loss_timing'] ?? '—')
                                    }}
                                </td>
                                <td>{{ $d['calving_date'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="muted">
                                    No records to display.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <footer class="footer">
                <div>
                    Benchmark guidance: Total pregnancy loss (dairy cattle)
                    normal range is <span class="value">8–15%</span>.
                </div>
                <div class="muted">
                    This report is generated from confirmed pregnancies in the
                    selected date range.
                </div>
            </footer>
        </div>

        <script>
            // Auto-open print dialog on load (common "professional print" behavior)
            window.addEventListener("load", () => {
                window.print();
            });
        </script>
    </body>
</html>
