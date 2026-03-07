<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Treatment Cost per Animal</title>

        <style>
            /* ---------- Base / screen ---------- */
            :root {
                --ink: #111827; /* gray-900 */
                --muted: #6b7280; /* gray-500 */
                --line: #e5e7eb; /* gray-200 */
                --soft: #f9fafb; /* gray-50 */
                --brand: #111827;

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
                grid-template-columns: 1.1fr 0.9fr;
                gap: 12px;
            }

            .kv {
                display: grid;
                grid-template-columns: 140px 1fr;
                row-gap: 6px;
                column-gap: 10px;
                font-size: 12px;
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

            .right {
                text-align: right;
            }

            .muted {
                color: var(--muted);
            }

            .summary-row td {
                background: #fff;
                font-weight: 700;
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
                        <h1>Treatment Cost per Animal</h1>
                        <div class="meta">Generated: {{ $generatedAt }}</div>
                    </div>
                </div>

                <div class="header-bottom">
                    <div class="kv">
                        <div class="k">Date range</div>
                        <div class="v">{{ $filters['from'] ?? '' }} → {{ $filters['to'] ?? '' }}</div>

                        <div class="k">Animal</div>
                        <div class="v">
                            @if(!empty($filters['animal_id']))
                                #{{ $filters['animal_id'] }}
                            @else
                                All animals
                            @endif
                        </div>

                        <div class="k">Search</div>
                        <div class="v">{{ $filters['q'] ?? '—' }}</div>

                        <div class="k">Sort</div>
                        <div class="v">{{ ($filters['sort'] ?? 'total_cost') }} ({{ $filters['direction'] ?? 'desc' }})</div>
                    </div>

                    <div class="stats">
                        <div class="stat">
                            <div class="k">Animals</div>
                            <div class="v">{{ (int) ($summary['animals'] ?? 0) }}</div>
                        </div>

                        <div class="stat">
                            <div class="k">Total cost</div>
                            <div class="v">{{ number_format((float) ($summary['total_cost'] ?? 0), 2) }}</div>
                        </div>

                        <div class="stat">
                            <div class="k">Treatment meds</div>
                            <div class="v">{{ number_format((float) ($summary['total_treatment_medication_cost'] ?? 0), 2) }}</div>
                        </div>

                        <div class="stat">
                            <div class="k">Vet + lab + other</div>
                            <div class="v">
                                {{
                                    number_format(
                                        (float) (($summary['total_vet_fee'] ?? 0) + ($summary['total_lab_cost'] ?? 0) + ($summary['total_other_cost'] ?? 0)),
                                        2
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <div class="toolbar">
                        <button class="btn" type="button" onclick="window.close()">Close</button>
                        <button class="btn btn-primary" type="button" onclick="window.print()">Print</button>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-h">
                    <h2>Cost breakdown</h2>
                    <div class="note">Showing {{ count($rows ?? []) }} animal(s)</div>
                </div>

                <div style="overflow: hidden">
                    <table>
                        <thead>
                            <tr>
                                <th>Animal</th>
                                <th class="right" style="width: 150px">Treatment meds</th>
                                <th class="right" style="width: 120px">Vet fee</th>
                                <th class="right" style="width: 110px">Lab</th>
                                <th class="right" style="width: 110px">Other</th>
                                <th class="right" style="width: 130px">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $r)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700">
                                            {{ $r['animal_tag'] ?? '' }} {{ $r['animal_name'] ?? '' }}
                                        </div>
                                        <div class="muted" style="font-size: 11px; margin-top: 2px">
                                            ID: {{ $r['animal_id'] ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="right">{{ number_format((float) ($r['treatment_medication_cost'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($r['vet_fee'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($r['lab_cost'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($r['other_cost'] ?? 0), 2) }}</td>
                                    <td class="right" style="font-weight: 700">
                                        {{ number_format((float) ($r['total_cost'] ?? 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 18px; text-align: center" class="muted">
                                        No data found for the selected filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if(!empty($rows) && count($rows) > 0)
                            <tfoot>
                                <tr class="summary-row">
                                    <td>Totals</td>
                                    <td class="right">{{ number_format((float) ($summary['total_treatment_medication_cost'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($summary['total_vet_fee'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($summary['total_lab_cost'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($summary['total_other_cost'] ?? 0), 2) }}</td>
                                    <td class="right">{{ number_format((float) ($summary['total_cost'] ?? 0), 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <div class="footer">
                <div>Treatment Cost per Animal</div>
                <div>Generated: {{ $generatedAt }}</div>
            </div>
        </div>

        <script>
            window.addEventListener("load", () => {
                setTimeout(() => window.print(), 350);
            });
        </script>
    </body>
</html>
