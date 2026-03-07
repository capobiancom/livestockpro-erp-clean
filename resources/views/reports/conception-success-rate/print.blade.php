<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Conception Success Rate - Print</title>

        <style>
            @page {
                size: A4;
                margin: 14mm 12mm;
            }

            :root {
                --text: #111827;
                --muted: #6b7280;
                --border: #e5e7eb;
                --bg: #ffffff;
                --panel: #f9fafb;
                --brand: #111827;
                --brand2: #2563eb;
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
                background: #f3f4f6;
            }

            /* App-like container, but prints clean */
            .page {
                max-width: 900px;
                margin: 24px auto;
                background: var(--bg);
                border: 1px solid var(--border);
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            }

            .topbar {
                padding: 18px 20px;
                border-bottom: 1px solid var(--border);
                background: linear-gradient(
                    135deg,
                    rgba(17, 24, 39, 1),
                    rgba(37, 99, 235, 0.95)
                );
                color: white;
            }

            .topbar .title {
                font-size: 18px;
                font-weight: 700;
                margin: 0;
                letter-spacing: 0.2px;
            }

            .topbar .subtitle {
                margin: 6px 0 0;
                font-size: 12px;
                opacity: 0.9;
            }

            .content {
                padding: 18px 20px 10px;
            }

            .meta {
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
            }

            @media (min-width: 700px) {
                .meta {
                    grid-template-columns: 1.4fr 1fr;
                    align-items: start;
                }
            }

            .card {
                border: 1px solid var(--border);
                background: var(--bg);
                border-radius: 10px;
                overflow: hidden;
            }

            .card .card-h {
                padding: 10px 12px;
                border-bottom: 1px solid var(--border);
                background: var(--panel);
                font-size: 12px;
                font-weight: 700;
                color: var(--text);
            }

            .card .card-b {
                padding: 12px;
            }

            .filters {
                display: grid;
                grid-template-columns: 1fr;
                gap: 8px;
                font-size: 12px;
            }

            .filters .row {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                padding: 6px 0;
                border-bottom: 1px dashed rgba(229, 231, 235, 0.9);
            }

            .filters .row:last-child {
                border-bottom: none;
            }

            .label {
                color: var(--muted);
                white-space: nowrap;
            }

            .value {
                color: var(--text);
                font-weight: 600;
                text-align: right;
            }

            .kpis {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            @media (min-width: 700px) {
                .kpis {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            .kpi {
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px 12px;
                background: white;
            }

            .kpi .k {
                font-size: 11px;
                color: var(--muted);
                text-transform: uppercase;
                letter-spacing: 0.4px;
                font-weight: 700;
            }

            .kpi .v {
                margin-top: 6px;
                font-size: 18px;
                font-weight: 800;
                color: var(--text);
            }

            .kpi.primary {
                border-color: rgba(37, 99, 235, 0.35);
                background: linear-gradient(
                    135deg,
                    rgba(37, 99, 235, 0.08),
                    rgba(17, 24, 39, 0.02)
                );
            }

            .kpi.primary .v {
                color: var(--brand2);
            }

            .section-title {
                margin: 16px 0 10px;
                font-size: 12px;
                font-weight: 800;
                color: var(--text);
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: baseline;
            }

            .section-title .hint {
                font-weight: 600;
                color: var(--muted);
                font-size: 11px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            thead th {
                text-align: left;
                background: var(--panel);
                padding: 10px 10px;
                border-bottom: 1px solid var(--border);
                border-top: 1px solid var(--border);
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.35px;
                color: #374151;
            }

            tbody td {
                padding: 10px 10px;
                border-bottom: 1px solid var(--border);
                vertical-align: top;
            }

            .right {
                text-align: right;
                white-space: nowrap;
            }

            .badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 999px;
                border: 1px solid var(--border);
                font-weight: 800;
                font-size: 11px;
                white-space: nowrap;
            }

            .badge.good {
                background: #ecfdf5;
                border-color: #a7f3d0;
                color: #047857;
            }

            .badge.mid {
                background: #fffbeb;
                border-color: #fde68a;
                color: #b45309;
            }

            .badge.low {
                background: #fff1f2;
                border-color: #fecdd3;
                color: #be123c;
            }

            .footer {
                padding: 10px 20px 16px;
                color: var(--muted);
                font-size: 11px;
                display: flex;
                justify-content: space-between;
                gap: 10px;
                border-top: 1px solid var(--border);
                background: #fff;
            }

            .actions {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 14px 20px 0;
            }

            .btn {
                appearance: none;
                border: 1px solid var(--border);
                background: white;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
            }

            .btn.primary {
                background: var(--brand);
                border-color: var(--brand);
                color: white;
            }

            @media print {
                body {
                    background: white;
                }

                .page {
                    max-width: none;
                    margin: 0;
                    border: none;
                    border-radius: 0;
                    box-shadow: none;
                }

                .actions {
                    display: none !important;
                }

                a[href]:after {
                    content: "";
                }
            }
        </style>
    </head>

    <body>
        <div class="page">
            <div class="topbar">
                <h1 class="title">Conception Success Rate</h1>
                <p class="subtitle">
                    (Confirmed Pregnancies ÷ Total Breeding Attempts) × 100
                </p>
            </div>

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

            <div class="content">
                <div class="meta">
                    <div class="card">
                        <div class="card-h">Filters</div>
                        <div class="card-b">
                            <div class="filters">
                                <div class="row">
                                    <div class="label">From</div>
                                    <div class="value">
                                        {{ $filters['from'] ?? '-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">To</div>
                                    <div class="value">
                                        {{ $filters['to'] ?? '-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Animal</div>
                                    <div class="value">
                                        {{ $filters['animal'] ?? 'All animals' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Service type</div>
                                    <div class="value">
                                        @php
                                            $st = $filters['service_type'] ?? 'all';
                                        @endphp

                                        @if ($st === 'ai')
                                            Artificial Insemination (AI)
                                        @elseif ($st === 'natural_mating')
                                            Natural Mating
                                        @elseif ($st === 'embryo_transfer')
                                            Embryo Transfer
                                        @else
                                            All services
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="label">Group by</div>
                                    <div class="value">
                                        {{ ($filters['group_by'] ?? 'service_type') === 'month' ? 'Month' : 'Service type' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-h">Summary</div>
                        <div class="card-b">
                            <div class="kpis">
                                <div class="kpi">
                                    <div class="k">Attempts</div>
                                    <div class="v">
                                        {{ $summary['total_attempts'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="kpi">
                                    <div class="k">Confirmed</div>
                                    <div class="v">
                                        {{ $summary['confirmed_pregnancies'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="kpi primary">
                                    <div class="k">Success rate</div>
                                    <div class="v">
                                        {{ $summary['conception_success_rate'] ?? 0 }}%
                                    </div>
                                </div>
                                <div class="kpi">
                                    <div class="k">Groups</div>
                                    <div class="v">{{ count($rows ?? []) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    <div>Results</div>
                    <div class="hint">Generated at: {{ $generatedAt }}</div>
                </div>

                <div class="card">
                    <div class="card-b" style="padding: 0">
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        {{ ($filters['group_by'] ?? 'service_type') === 'month' ? 'Month' : 'Service type' }}
                                    </th>
                                    <th class="right">Attempts</th>
                                    <th class="right">Confirmed</th>
                                    <th class="right">Success rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (empty($rows) || count($rows) === 0)
                                    <tr>
                                        <td colspan="4" style="padding: 18px 10px; color: #6b7280; text-align: center;">
                                            No breeding attempts found for the selected filters.
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($rows as $r)
                                        @php
                                            $rate = (float) ($r['rate'] ?? 0);
                                            $badge = 'low';
                                            if ($rate >= 70) {
                                                $badge = 'good';
                                            } elseif ($rate >= 40) {
                                                $badge = 'mid';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $r['group'] ?? '-' }}</td>
                                            <td class="right">
                                                {{ $r['attempts'] ?? 0 }}
                                            </td>
                                            <td class="right">
                                                {{ $r['confirmed'] ?? 0 }}
                                            </td>
                                            <td class="right">
                                                <span class="badge {{ $badge }}">
                                                    {{ $rate }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="footer">
                <div>
                    This report is for internal use. Values are based on
                    reproduction records and confirmed pregnancy dates.
                </div>
                <div>
                    Page <span class="pageNumber"></span> of
                    <span class="totalPages"></span>
                </div>
            </div>
        </div>

        <script>
            // Best-effort page numbers (works in some browsers/print engines)
            (function () {
                try {
                    const pn = document.querySelector(".pageNumber");
                    const tp = document.querySelector(".totalPages");
                    if (pn) pn.textContent = "";
                    if (tp) tp.textContent = "";
                } catch (e) {}
            })();
        </script>
    </body>
</html>
