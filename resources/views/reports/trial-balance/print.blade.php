<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trial Balance Print</title>

    <style>
        :root{
            --ink:#0f172a;
            --muted:#64748b;
            --line:#e2e8f0;
            --soft:#f8fafc;
            --brand:#2563eb;
            --good:#047857;
            --warn:#b45309;
        }

        *{ box-sizing:border-box; }
        html,body{ height:100%; }
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color:var(--ink);
            background:#fff;
        }

        .page{
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 14mm 14mm 16mm;
        }

        .header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            padding-bottom:10px;
            border-bottom:2px solid var(--ink);
        }

        .brand{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .mark{
            width:40px;
            height:40px;
            border-radius:10px;
            background:linear-gradient(135deg, #0ea5e9 0%, #2563eb 45%, #1d4ed8 100%);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-weight:800;
            letter-spacing:.5px;
            font-size:16px;
            flex: 0 0 auto;
        }

        .title h1{
            margin:0;
            font-size:20px;
            line-height:1.2;
            letter-spacing:.2px;
        }
        .title .subtitle{
            margin-top:4px;
            font-size:12px;
            color:var(--muted);
        }

        .meta{
            text-align:right;
            font-size:12px;
            color:var(--muted);
            line-height:1.6;
            min-width: 220px;
        }
        .meta b{ color:var(--ink); font-weight:700; }
        .pill{
            display:inline-block;
            padding:2px 8px;
            border-radius:999px;
            border:1px solid var(--line);
            background:var(--soft);
            color:var(--ink);
            font-weight:700;
            font-size:11px;
            margin-top:4px;
        }
        .pill.ok{ border-color:#a7f3d0; background:#ecfdf5; color:var(--good); }
        .pill.warn{ border-color:#fde68a; background:#fffbeb; color:var(--warn); }

        .summary{
            display:grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap:10px;
            margin-top:12px;
        }
        .card{
            border:1px solid var(--line);
            background:#fff;
            border-radius:12px;
            padding:10px 12px;
        }
        .card .label{
            color:var(--muted);
            font-size:11px;
            letter-spacing:.6px;
            text-transform:uppercase;
            font-weight:800;
        }
        .card .value{
            margin-top:4px;
            font-size:16px;
            font-weight:800;
        }
        .card .hint{
            margin-top:4px;
            font-size:11px;
            color:var(--muted);
        }
        .value.debit{ color:var(--good); }
        .value.credit{ color:#1d4ed8; }
        .value.diff.ok{ color:var(--good); }
        .value.diff.bad{ color:var(--warn); }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:12px;
            font-size:12px;
        }
        thead th{
            text-align:left;
            padding:8px 8px;
            border-bottom:1px solid var(--line);
            background: var(--soft);
            color: var(--muted);
            font-weight:800;
            text-transform:uppercase;
            letter-spacing:.4px;
            font-size:11px;
        }
        tbody td{
            padding:8px 8px;
            border-bottom:1px solid var(--line);
            vertical-align:top;
        }
        tbody tr:nth-child(even){
            background:#fcfdff;
        }
        .num{ text-align:right; font-variant-numeric: tabular-nums; }
        .acct{
            font-weight:800;
            color:var(--ink);
            line-height:1.25;
        }
        .sub{
            margin-top:2px;
            font-size:11px;
            color:var(--muted);
        }

        tfoot td{
            padding:9px 8px;
            border-top:2px solid var(--ink);
            background: var(--soft);
            font-weight:900;
        }

        .footer{
            margin-top:12px;
            padding-top:8px;
            border-top:1px solid var(--line);
            display:flex;
            justify-content:space-between;
            gap:12px;
            color:var(--muted);
            font-size:11px;
            line-height:1.5;
        }

        .actions{
            position: fixed;
            top: 12px;
            right: 12px;
            display:flex;
            gap:8px;
            z-index: 1000;
        }
        .btn{
            border:1px solid var(--line);
            background:#fff;
            color:var(--ink);
            padding:8px 10px;
            border-radius:10px;
            font-size:12px;
            font-weight:800;
            cursor:pointer;
        }
        .btn.primary{
            background:var(--brand);
            border-color:var(--brand);
            color:#fff;
        }

        @page { size: A4; margin: 10mm; }
        @media print{
            body{ background:#fff; }
            .page{ padding: 0; width: auto; min-height: auto; }
            .actions{ display:none !important; }
            a{ color:inherit; text-decoration:none; }
        }
    </style>
</head>

<body>
<div class="actions">
    <button class="btn" onclick="window.close()">Close</button>
    <button class="btn primary" onclick="window.print()">Print</button>
</div>

<div class="page">
    @php
        $currency = config('app.currency_symbol') ?? 'BDT';
        $diff = (float)($totals['difference'] ?? 0);
        $isBalanced = abs($diff) < 0.00001;

        $fmt = function ($v) use ($currency) {
            $n = (float)($v ?? 0);
            return $currency . ' ' . number_format($n, 2);
        };
    @endphp

    <div class="header">
        <div class="brand">
            <div class="mark">TB</div>
            <div class="title">
                <h1>Trial Balance</h1>
                <div class="subtitle">
                    Posted entries from <b>{{ $from }}</b> to <b>{{ $to }}</b>
                    @if(!empty($includeZero))
                        <span class="pill" style="margin-left:8px;">Including zero-activity accounts</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="meta">
            <div><b>Generated:</b> {{ now()->format('Y-m-d H:i') }}</div>
            <div><b>Rows:</b> {{ is_countable($rows) ? count($rows) : 0 }}</div>
            <div class="pill {{ $isBalanced ? 'ok' : 'warn' }}">
                {{ $isBalanced ? 'Balanced' : 'Out of balance' }}
            </div>
        </div>
    </div>

    <div class="summary">
        <div class="card">
            <div class="label">Total Debit (Balances)</div>
            <div class="value debit">{{ $fmt($totals['debit_balance'] ?? 0) }}</div>
            <div class="hint">Net debit balances across all accounts.</div>
        </div>
        <div class="card">
            <div class="label">Total Credit (Balances)</div>
            <div class="value credit">{{ $fmt($totals['credit_balance'] ?? 0) }}</div>
            <div class="hint">Net credit balances across all accounts.</div>
        </div>
        <div class="card">
            <div class="label">Difference</div>
            <div class="value diff {{ $isBalanced ? 'ok' : 'bad' }}">{{ $fmt($totals['difference'] ?? 0) }}</div>
            <div class="hint">Should be 0.00 when all posted entries are balanced.</div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width:34%;">Account</th>
            <th class="num" style="width:16%;">Debit Total</th>
            <th class="num" style="width:16%;">Credit Total</th>
            <th class="num" style="width:17%;">Debit Balance</th>
            <th class="num" style="width:17%;">Credit Balance</th>
        </tr>
        </thead>

        <tbody>
        @forelse($rows as $r)
            <tr>
                <td>
                    <div class="acct">{{ $r['name'] ?? '' }}</div>
                    <div class="sub">
                        {{ $r['code'] ?? '' }}
                        @if(!empty($r['type']))
                            <span style="margin:0 6px;">•</span>{{ $r['type'] }}
                        @endif
                    </div>
                </td>
                <td class="num">{{ $fmt($r['debit_total'] ?? 0) }}</td>
                <td class="num">{{ $fmt($r['credit_total'] ?? 0) }}</td>
                <td class="num" style="font-weight:900; color: var(--good);">{{ $fmt($r['debit_balance'] ?? 0) }}</td>
                <td class="num" style="font-weight:900; color: #1d4ed8;">{{ $fmt($r['credit_balance'] ?? 0) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:18px 8px; color:var(--muted);">
                    No posted activity found for the selected period.
                </td>
            </tr>
        @endforelse
        </tbody>

        <tfoot>
        <tr>
            <td>Totals</td>
            <td class="num">{{ $fmt($totals['debit_total'] ?? 0) }}</td>
            <td class="num">{{ $fmt($totals['credit_total'] ?? 0) }}</td>
            <td class="num" style="color: var(--good);">{{ $fmt($totals['debit_balance'] ?? 0) }}</td>
            <td class="num" style="color: #1d4ed8;">{{ $fmt($totals['credit_balance'] ?? 0) }}</td>
        </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div>
            <b style="color:var(--ink);">Notes:</b>
            Trial Balance is generated from posted journal entries only. If the report is out of balance, review unposted vouchers, unbalanced journal entries, or incorrect postings.
        </div>
        <div style="text-align:right;">
            <div><b style="color:var(--ink);">Document:</b> Trial Balance</div>
            <div><b style="color:var(--ink);">Period:</b> {{ $from }} → {{ $to }}</div>
        </div>
    </div>
</div>

<script>
    // Auto-open print dialog shortly after render
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 250);
    });
</script>
</body>
</html>
