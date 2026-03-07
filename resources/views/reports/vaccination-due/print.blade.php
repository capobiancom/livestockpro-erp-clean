<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Vaccination Due Report</title>

        <style>
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --brand: #111827;
                --danger: #b91c1c;
                --warn: #b45309;
                --ok: #047857;
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

            .pill-danger {
                background: #fef2f2;
                border-color: #fecaca;
                color: var(--danger);
            }

            .pill-warn {
                background: #fffbeb;
                border-color: #fde68a;
                color: var(--warn);
            }

            .pill-ok {
                background: #ecfdf5;
                border-color: #a7f3d0;
                color: var(--ok);
            }

            .muted {
                color: var(--muted);
            }

            .right {
                text-align: right;
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
        <div class="page">
            <div class="header">
                <div class="header-top">
                    <div class="header-title">
                        <h1>Vaccination Due Report</h1>
                        <div class="meta">Generated: {{ $generatedAt }}</div>
                    </div>
                </div>

                <div class="header-bottom">
                    <div class="kv">
                        <div class="k">Date range</div>
                        <div class="v">{{ $summary['from'] }} → {{ $summary['to'] }}</div>

                        <div class="k">Animal</div>
                        <div class="v">
                            @php
                                $animalLabel = 'All animals';
                                if (!empty($filters['animal_id'])) {
                                    $a = collect($animals)->firstWhere('id', (int) $filters['animal_id']);
                                    $animalLabel = $a ? ($a['label'] ?? 'Selected animal') : 'Selected animal';
                                }
                            @endphp
                            {{ $animalLabel }}
                        </div>

                        <div class="k">Status filter</div>
                        <div class="v">
                            @php
                                $statusLabel = $filters['status'] ?? 'all';
                                if ($statusLabel === 'overdue') {
                                    $statusLabel = 'Overdue';
                                } elseif ($statusLabel === 'due') {
                                    $statusLabel = 'Due today';
                                } elseif ($statusLabel === 'upcoming') {
                                    $statusLabel = 'Upcoming';
                                } else {
                                    $statusLabel = 'All';
                                }
                            @endphp
                            {{ $statusLabel }}
                        </div>

                        <div class="k">Record cap</div>
                        <div class="v">Up to 5,000 rows</div>
                    </div>

                    <div class="stats">
                        <div class="stat">
                            <div class="k">Total</div>
                            <div class="v">{{ $summary['total'] ?? 0 }}</div>
                        </div>
                        <div class="stat">
                            <div class="k">Overdue</div>
                            <div class="v" style="color: var(--danger)">{{ $summary['overdue'] ?? 0 }}</div>
                        </div>
                        <div class="stat">
                            <div class="k">Due today</div>
                            <div class="v" style="color: var(--warn)">{{ $summary['due_today'] ?? 0 }}</div>
                        </div>
                        <div class="stat">
                            <div class="k">Upcoming</div>
                            <div class="v" style="color: var(--ok)">{{ $summary['upcoming'] ?? 0 }}</div>
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
                    <h2>Due List</h2>
                    <div class="note">Showing {{ count($rows ?? []) }} record(s)</div>
                </div>

                <div style="overflow: hidden">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 92px">Due date</th>
                                <th style="width: 170px">Animal</th>
                                <th style="width: 150px">Disease</th>
                                <th style="width: 150px">Staff</th>
                                <th style="width: 86px" class="right">Days left</th>
                                <th style="width: 110px">Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                @php
                                    $pillClass = 'pill-ok';
                                    if (($r['status'] ?? '') === 'Overdue') {
                                        $pillClass = 'pill-danger';
                                    } elseif (($r['status'] ?? '') === 'Due Today') {
                                        $pillClass = 'pill-warn';
                                    }
                                @endphp

                                <tr>
                                    <td>{{ $r['due_date'] ?? '—' }}</td>
                                    <td>{{ $r['animal'] ?? '—' }}</td>
                                    <td>{{ $r['disease'] ?? '—' }}</td>
                                    <td class="muted">{{ $r['staff'] ?? '—' }}</td>
                                    <td class="right">{{ $r['days_left'] ?? '—' }}</td>
                                    <td>
                                        <span class="pill {{ $pillClass }}">
                                            {{ $r['status'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="muted">{{ $r['notes'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="padding: 18px; text-align: center" class="muted">
                                        No vaccinations due for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="footer">
                <div>Vaccination Due Report</div>
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
