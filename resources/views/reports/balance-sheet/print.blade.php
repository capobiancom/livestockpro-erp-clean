<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, viewport-fit=cover"
        />
        <title>Balance Sheet (as of {{ $asOf }})</title>

        <style>
            /* Page + print setup */
            @page {
                size: A4;
                margin: 14mm 12mm 16mm 12mm;
            }

            html,
            body {
                padding: 0;
                margin: 0;
            }

            body {
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI,
                    Roboto, Helvetica, Arial, "Apple Color Emoji",
                    "Segoe UI Emoji";
                color: #0f172a;
                font-size: 12px;
                line-height: 1.35;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                width: 100%;
            }

            .topbar {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 16px;
                padding-bottom: 12px;
                border-bottom: 1px solid #e2e8f0;
                margin-bottom: 14px;
            }

            .brand {
                display: flex;
                align-items: flex-start;
                gap: 10px;
            }

            .logo {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                background: linear-gradient(135deg, #0f172a, #334155);
            }

            .title-wrap h1 {
                margin: 0;
                font-size: 18px;
                letter-spacing: 0.2px;
            }

            .title-wrap .subtitle {
                margin-top: 2px;
                color: #475569;
                font-size: 12px;
            }

            .meta {
                text-align: right;
                color: #475569;
                font-size: 11px;
                min-width: 180px;
            }

            .meta .chip {
                display: inline-block;
                margin-top: 6px;
                padding: 4px 8px;
                border: 1px solid #cbd5e1;
                border-radius: 999px;
                color: #0f172a;
                font-weight: 600;
                font-size: 11px;
                background: #f8fafc;
            }

            .summary {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 10px;
                margin: 0 0 14px 0;
            }

            .card {
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 10px 12px;
                background: #ffffff;
            }

            .card .label {
                color: #64748b;
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 700;
            }

            .card .value {
                margin-top: 6px;
                font-size: 14px;
                font-weight: 800;
            }

            .grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                align-items: start;
            }

            .panel {
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                overflow: hidden;
                background: #ffffff;
            }

            .panel-header {
                padding: 10px 12px;
                color: white;
                font-weight: 800;
                font-size: 12px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            .panel-header .hint {
                font-weight: 600;
                font-size: 11px;
                opacity: 0.9;
            }

            .bg-asset {
                background: linear-gradient(90deg, #065f46, #0f766e);
            }
            .bg-liability {
                background: linear-gradient(90deg, #9a3412, #b45309);
            }
            .bg-equity {
                background: linear-gradient(90deg, #3730a3, #2563eb);
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            thead th {
                text-align: left;
                font-size: 10px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #475569;
                padding: 8px 12px;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
            }

            tbody td {
                padding: 7px 12px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: top;
            }

            tbody tr:last-child td {
                border-bottom: none;
            }

            .muted {
                color: #64748b;
                font-size: 10px;
                margin-top: 1px;
            }

            .num {
                text-align: right;
                font-variant-numeric: tabular-nums;
                white-space: nowrap;
                font-weight: 700;
            }

            tfoot td {
                padding: 9px 12px;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                font-weight: 800;
            }

            .equation {
                margin-top: 12px;
                border: 1px dashed #cbd5e1;
                border-radius: 12px;
                padding: 10px 12px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                background: #ffffff;
            }

            .equation .left .label {
                color: #64748b;
                font-size: 10px;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-weight: 700;
            }

            .equation .left .text {
                margin-top: 4px;
                font-size: 12px;
                color: #334155;
            }

            .equation .right {
                text-align: right;
            }

            .equation .right .small {
                color: #64748b;
                font-size: 10px;
            }

            .equation .right .big {
                margin-top: 4px;
                font-size: 14px;
                font-weight: 900;
                font-variant-numeric: tabular-nums;
            }

            .equation .right .diff {
                margin-top: 2px;
                font-size: 11px;
                font-weight: 700;
                font-variant-numeric: tabular-nums;
            }

            .ok {
                color: #047857;
            }
            .warn {
                color: #b45309;
            }

            .footer {
                margin-top: 14px;
                padding-top: 10px;
                border-top: 1px solid #e2e8f0;
                color: #64748b;
                font-size: 10px;
                display: flex;
                justify-content: space-between;
                gap: 12px;
            }

            .no-print {
                display: none;
            }

            /* Prevent ugly page breaks in the middle of rows/panels */
            .panel,
            .card,
            .equation {
                break-inside: avoid;
            }

            tr {
                break-inside: avoid;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="topbar">
                <div class="brand">
                    <div class="logo" aria-hidden="true"></div>
                    <div class="title-wrap">
                        <h1>Balance Sheet</h1>
                        <div class="subtitle">
                            Statement of financial position as of
                            <strong>{{ $asOf }}</strong>
                        </div>
                    </div>
                </div>

                <div class="meta">
                    <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
                    <div class="chip">
                        {{ number_format((float) ($totals['difference'] ?? 0), 2) == 0 ? 'Balanced' : 'Out of balance' }}
                    </div>
                </div>
            </div>

            <div class="summary">
                <div class="card">
                    <div class="label">Total Assets</div>
                    <div class="value">{{ number_format((float) ($totals['assets'] ?? 0), 2) }}</div>
                </div>
                <div class="card">
                    <div class="label">Total Liabilities</div>
                    <div class="value">{{ number_format((float) ($totals['liabilities'] ?? 0), 2) }}</div>
                </div>
                <div class="card">
                    <div class="label">Total Equity</div>
                    <div class="value">{{ number_format((float) ($totals['equity'] ?? 0), 2) }}</div>
                </div>
            </div>

            @php
                $assets = collect($rows)->where('type', 'asset')->values();
                $liabilities = collect($rows)->where('type', 'liability')->values();
                $equity = collect($rows)->where('type', 'equity')->values();
            @endphp

            <div class="grid">
                <div class="panel">
                    <div class="panel-header bg-asset">
                        <div>Assets</div>
                        <div class="hint">Resources owned by the business</div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th style="text-align: right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $r)
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a">
                                            {{ $r['name'] ?? '' }}
                                        </div>
                                        <div class="muted">{{ $r['code'] ?? '' }}</div>
                                    </td>
                                    <td class="num">
                                        {{ number_format((float) ($r['balance'] ?? 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="text-align: center; padding: 14px; color: #64748b">
                                        No asset balances found for this date.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total Assets</td>
                                <td class="num">{{ number_format((float) ($totals['assets'] ?? 0), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="display: grid; grid-template-rows: auto auto; gap: 12px">
                    <div class="panel">
                        <div class="panel-header bg-liability">
                            <div>Liabilities</div>
                            <div class="hint">Obligations owed to others</div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th style="text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($liabilities as $r)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a">
                                                {{ $r['name'] ?? '' }}
                                            </div>
                                            <div class="muted">{{ $r['code'] ?? '' }}</div>
                                        </td>
                                        <td class="num">
                                            {{ number_format((float) ($r['balance'] ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 14px; color: #64748b">
                                            No liability balances found for this date.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total Liabilities</td>
                                    <td class="num">{{ number_format((float) ($totals['liabilities'] ?? 0), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="panel">
                        <div class="panel-header bg-equity">
                            <div>Equity</div>
                            <div class="hint">Owner’s interest in the business</div>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th style="text-align: right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($equity as $r)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a">
                                                {{ $r['name'] ?? '' }}
                                            </div>
                                            <div class="muted">{{ $r['code'] ?? '' }}</div>
                                        </td>
                                        <td class="num">
                                            {{ number_format((float) ($r['balance'] ?? 0), 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" style="text-align: center; padding: 14px; color: #64748b">
                                            No equity balances found for this date.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>Total Equity</td>
                                    <td class="num">{{ number_format((float) ($totals['equity'] ?? 0), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @php
                $diff = (float) ($totals['difference'] ?? 0);
                $balanced = abs($diff) < 0.00001;
            @endphp

            <div class="equation">
                <div class="left">
                    <div class="label">Accounting equation</div>
                    <div class="text">Assets should equal Liabilities + Equity</div>
                </div>

                <div class="right">
                    <div class="small">Liabilities + Equity</div>
                    <div class="big">
                        {{ number_format((float) ($totals['liabilities_and_equity'] ?? 0), 2) }}
                    </div>
                    <div class="diff {{ $balanced ? 'ok' : 'warn' }}">
                        Difference: {{ number_format($diff, 2) }}
                    </div>
                </div>
            </div>

            <div class="footer">
                <div>
                    Notes: This report is generated from posted journal entries up to
                    and including the selected date.
                </div>
                <div style="text-align: right">
                    Page 1 of 1
                </div>
            </div>
        </div>

        <script>
            // Auto open the print dialog when the new tab opens.
            window.addEventListener("load", () => {
                setTimeout(() => window.print(), 250);
            });
        </script>
    </body>
</html>
