<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profit & Loss ({{ $from }} to {{ $to }})</title>

    <style>
        :root{
            --ink:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
            --soft:#f1f5f9;
            --income:#047857;
            --expense:#be123c;
        }

        *{ box-sizing:border-box; }
        html,body{ height:100%; }
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color:var(--ink);
            background:#fff;
        }

        /* Page */
        .page{
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 14mm 14mm 16mm;
        }

        /* Header */
        .header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            padding-bottom:10px;
            border-bottom: 2px solid var(--border);
        }
        .brand{
            display:flex;
            align-items:flex-start;
            gap:12px;
            min-width: 0;
        }
        .logo{
            width:42px;
            height:42px;
            border-radius:10px;
            background:linear-gradient(135deg,#0ea5e9,#1d4ed8);
            box-shadow: 0 6px 16px rgba(2, 6, 23, 0.12);
            flex:0 0 auto;
        }
        .brand h1{
            margin:0;
            font-size:18px;
            letter-spacing:.2px;
        }
        .brand .sub{
            margin-top:2px;
            font-size:12px;
            color:var(--muted);
        }

        .meta{
            text-align:right;
            font-size:12px;
            color:var(--muted);
            line-height:1.5;
            white-space:nowrap;
        }
        .meta strong{ color:var(--ink); font-weight:600; }

        /* Summary cards */
        .summary{
            display:grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap:10px;
            margin-top:12px;
        }
        .card{
            border:1px solid var(--border);
            border-radius:12px;
            padding:10px 12px;
            background:#fff;
        }
        .card .label{
            font-size:11px;
            color:var(--muted);
            text-transform:uppercase;
            letter-spacing:.06em;
            font-weight:700;
        }
        .card .value{
            margin-top:6px;
            font-size:16px;
            font-weight:800;
        }
        .income{ color:var(--income); }
        .expense{ color:var(--expense); }

        /* Tables */
        .grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap:12px;
            margin-top:14px;
        }
        .box{
            border:1px solid var(--border);
            border-radius:12px;
            overflow:hidden;
        }
        .box .box-title{
            padding:10px 12px;
            font-size:12px;
            font-weight:800;
            letter-spacing:.04em;
            text-transform:uppercase;
            color:#fff;
        }
        .box.income .box-title{
            background: linear-gradient(90deg, #047857, #0d9488);
        }
        .box.expense .box-title{
            background: linear-gradient(90deg, #be123c, #db2777);
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        thead th{
            background: var(--soft);
            color: var(--muted);
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:.06em;
            padding:8px 12px;
            text-align:left;
            border-bottom:1px solid var(--border);
        }
        thead th:last-child{ text-align:right; }
        tbody td{
            padding:8px 12px;
            border-bottom:1px solid var(--border);
            vertical-align:top;
        }
        tbody tr:last-child td{ border-bottom:none; }
        td.amount{
            text-align:right;
            font-variant-numeric: tabular-nums;
            white-space:nowrap;
            font-weight:700;
        }
        .acc-name{
            font-weight:700;
            font-size:12.5px;
            margin:0;
        }
        .acc-code{
            margin-top:2px;
            font-size:11px;
            color:var(--muted);
        }
        tfoot td{
            padding:10px 12px;
            background: var(--soft);
            border-top:1px solid var(--border);
            font-weight:800;
        }
        tfoot td:last-child{
            text-align:right;
            font-variant-numeric: tabular-nums;
            white-space:nowrap;
        }

        /* Footer */
        .footer{
            margin-top:14px;
            padding-top:10px;
            border-top: 1px solid var(--border);
            display:flex;
            justify-content:space-between;
            gap:12px;
            color:var(--muted);
            font-size:11px;
        }

        /* Print tweaks */
        @page{
            size: A4;
            margin: 10mm;
        }
        @media print{
            .page{ width:auto; min-height:auto; padding:0; margin:0; }
            a[href]:after{ content:""; }
        }
    </style>
</head>
<body>
    @php
        $currency = config('app.currency_symbol', config('app.currency', 'BDT'));

        $incomeRows = collect($rows)->where('type', 'income')->filter(fn($r) => (float)($r['amount'] ?? 0) != 0)->values();
        $expenseRows = collect($rows)->where('type', 'expense')->filter(fn($r) => (float)($r['amount'] ?? 0) != 0)->values();

        $fmt = function($v) use ($currency) {
            $n = (float)($v ?? 0);
            return $currency.' '.number_format($n, 2, '.', ',');
        };
    @endphp

    <div class="page">
        <div class="header">
            <div class="brand">
                <div class="logo" aria-hidden="true"></div>
                <div style="min-width:0">
                    <h1>Profit & Loss</h1>
                    <div class="sub">Income statement for the selected period</div>
                    <div class="sub"><strong>Period:</strong> {{ $from }} → {{ $to }}</div>
                </div>
            </div>

            <div class="meta">
                <div><strong>Generated:</strong> {{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        <div class="summary">
            <div class="card">
                <div class="label">Total Income</div>
                <div class="value income">{{ $fmt($totals['total_income'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="label">Total Expenses</div>
                <div class="value expense">{{ $fmt($totals['total_expenses'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="label">Net Result</div>
                @php $net = (float)($totals['net_profit'] ?? 0); @endphp
                <div class="value" style="color: {{ $net >= 0 ? 'var(--income)' : 'var(--expense)' }}">
                    {{ $fmt($net) }}
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="box income">
                <div class="box-title">Income</div>
                <table>
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($incomeRows as $r)
                        <tr>
                            <td>
                                <div class="acc-name">{{ $r['name'] ?? '-' }}</div>
                                <div class="acc-code">{{ $r['code'] ?? '' }}</div>
                            </td>
                            <td class="amount income">{{ $fmt($r['amount'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding:14px 12px; color: var(--muted); text-align:center;">
                                No income found for this period.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total Income</td>
                        <td class="income">{{ $fmt($totals['total_income'] ?? 0) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="box expense">
                <div class="box-title">Expenses</div>
                <table>
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($expenseRows as $r)
                        <tr>
                            <td>
                                <div class="acc-name">{{ $r['name'] ?? '-' }}</div>
                                <div class="acc-code">{{ $r['code'] ?? '' }}</div>
                            </td>
                            <td class="amount expense">{{ $fmt($r['amount'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding:14px 12px; color: var(--muted); text-align:center;">
                                No expenses found for this period.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr>
                        <td>Total Expenses</td>
                        <td class="expense">{{ $fmt($totals['total_expenses'] ?? 0) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="footer">
            <div>
                Notes: Generated from posted journal entries within the selected period.
            </div>
            <div>
                Page 1
            </div>
        </div>
    </div>

    <script>
        // Auto-open print dialog when the page loads
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
