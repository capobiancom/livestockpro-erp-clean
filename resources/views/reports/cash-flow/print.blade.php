<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cash Flow ({{ $from }} to {{ $to }})</title>

    <style>
        :root{
            --ink:#0f172a;
            --muted:#64748b;
            --border:#e2e8f0;
            --soft:#f1f5f9;

            --op:#1d4ed8;
            --inv:#047857;
            --fin:#be123c;

            --pos:#047857;
            --neg:#be123c;
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

        /* KPI cards */
        .summary{
            display:grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
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
            font-weight:800;
        }
        .card .value{
            margin-top:6px;
            font-size:15.5px;
            font-weight:900;
            font-variant-numeric: tabular-nums;
            white-space:nowrap;
        }
        .op{ color:var(--op); }
        .inv{ color:var(--inv); }
        .fin{ color:var(--fin); }
        .pos{ color:var(--pos); }
        .neg{ color:var(--neg); }

        /* Section boxes */
        .grid{
            display:grid;
            grid-template-columns: 1fr;
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
            font-weight:900;
            letter-spacing:.04em;
            text-transform:uppercase;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
        }
        .box .box-title .hint{
            font-weight:700;
            letter-spacing:0;
            text-transform:none;
            font-size:11.5px;
            color: rgba(255,255,255,.85);
        }

        .box.op .box-title{ background: linear-gradient(90deg, #1d4ed8, #2563eb); }
        .box.inv .box-title{ background: linear-gradient(90deg, #047857, #0d9488); }
        .box.fin .box-title{ background: linear-gradient(90deg, #be123c, #db2777); }

        table{ width:100%; border-collapse:collapse; }
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
            font-weight:800;
        }

        .acc-name{
            font-weight:800;
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
            font-weight:900;
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

        /* Small helpers */
        .badge{
            display:inline-flex;
            align-items:center;
            gap:6px;
            padding:4px 8px;
            border-radius:999px;
            border:1px solid var(--border);
            background:#fff;
            font-size:11px;
            font-weight:800;
            color:var(--muted);
            white-space:nowrap;
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

    $operatingRows = collect($rows['operating'] ?? [])->filter(fn($r) => (float)($r['movement'] ?? 0) != 0)->values();
    $investingRows = collect($rows['investing'] ?? [])->filter(fn($r) => (float)($r['movement'] ?? 0) != 0)->values();
    $financingRows = collect($rows['financing'] ?? [])->filter(fn($r) => (float)($r['movement'] ?? 0) != 0)->values();

    $fmt = function($v) use ($currency) {
        $n = (float)($v ?? 0);
        return $currency.' '.number_format($n, 2, '.', ',');
    };

    $signedClass = function($v){
        $n = (float)($v ?? 0);
        if($n > 0) return 'pos';
        if($n < 0) return 'neg';
        return '';
    };

    $net = (float)($totals['net_change'] ?? 0);
@endphp

<div class="page">
    <div class="header">
        <div class="brand">
            <div class="logo" aria-hidden="true"></div>
            <div style="min-width:0">
                <h1>Cash Flow</h1>
                <div class="sub">Statement of cash flows (indirect approximation)</div>
                <div class="sub"><strong>Period:</strong> {{ $from }} → {{ $to }}</div>
            </div>
        </div>

        <div class="meta">
            <div><strong>Generated:</strong> {{ now()->format('Y-m-d H:i') }}</div>
            <div class="badge"><strong>Net change:</strong>&nbsp;<span class="{{ $net >= 0 ? 'pos' : 'neg' }}">{{ $fmt($net) }}</span></div>
        </div>
    </div>

    <div class="summary">
        <div class="card">
            <div class="label">Operating</div>
            <div class="value op {{ $signedClass($totals['operating'] ?? 0) }}">{{ $fmt($totals['operating'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="label">Investing</div>
            <div class="value inv {{ $signedClass($totals['investing'] ?? 0) }}">{{ $fmt($totals['investing'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="label">Financing</div>
            <div class="value fin {{ $signedClass($totals['financing'] ?? 0) }}">{{ $fmt($totals['financing'] ?? 0) }}</div>
        </div>
        <div class="card">
            <div class="label">Net change</div>
            <div class="value {{ $net >= 0 ? 'pos' : 'neg' }}">{{ $fmt($net) }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="box op">
            <div class="box-title">
                <div>Operating Activities</div>
                <div class="hint">Cash generated from core operations</div>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Account</th>
                    <th>Cash impact</th>
                </tr>
                </thead>
                <tbody>
                @forelse($operatingRows as $r)
                    <tr>
                        <td>
                            <div class="acc-name">{{ $r['name'] ?? '-' }}</div>
                            <div class="acc-code">{{ $r['code'] ?? '' }}</div>
                        </td>
                        <td class="amount {{ $signedClass($r['movement'] ?? 0) }}">{{ $fmt($r['movement'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding:14px 12px; color: var(--muted); text-align:center;">
                            No operating movements found for this period.
                        </td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr>
                    <td>Net cash from operating activities</td>
                    <td class="{{ $signedClass($totals['operating'] ?? 0) }}">{{ $fmt($totals['operating'] ?? 0) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="box inv">
            <div class="box-title">
                <div>Investing Activities</div>
                <div class="hint">Cash used for long-term assets and investments</div>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Account</th>
                    <th>Cash impact</th>
                </tr>
                </thead>
                <tbody>
                @forelse($investingRows as $r)
                    <tr>
                        <td>
                            <div class="acc-name">{{ $r['name'] ?? '-' }}</div>
                            <div class="acc-code">{{ $r['code'] ?? '' }}</div>
                        </td>
                        <td class="amount {{ $signedClass($r['movement'] ?? 0) }}">{{ $fmt($r['movement'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding:14px 12px; color: var(--muted); text-align:center;">
                            No investing movements found for this period.
                        </td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr>
                    <td>Net cash from investing activities</td>
                    <td class="{{ $signedClass($totals['investing'] ?? 0) }}">{{ $fmt($totals['investing'] ?? 0) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="box fin">
            <div class="box-title">
                <div>Financing Activities</div>
                <div class="hint">Cash from borrowings, repayments, and equity movements</div>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Account</th>
                    <th>Cash impact</th>
                </tr>
                </thead>
                <tbody>
                @forelse($financingRows as $r)
                    <tr>
                        <td>
                            <div class="acc-name">{{ $r['name'] ?? '-' }}</div>
                            <div class="acc-code">{{ $r['code'] ?? '' }}</div>
                        </td>
                        <td class="amount {{ $signedClass($r['movement'] ?? 0) }}">{{ $fmt($r['movement'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="padding:14px 12px; color: var(--muted); text-align:center;">
                            No financing movements found for this period.
                        </td>
                    </tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr>
                    <td>Net cash from financing activities</td>
                    <td class="{{ $signedClass($totals['financing'] ?? 0) }}">{{ $fmt($totals['financing'] ?? 0) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="footer">
        <div>
            Notes: Generated from posted journal entries within the selected period and grouped by account type.
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
