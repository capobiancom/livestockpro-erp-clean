<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI vs Natural Breeding Success - Print</title>

    <style>
        :root{
            --text:#111827;
            --muted:#6b7280;
            --border:#e5e7eb;
            --bg:#ffffff;
            --soft:#f9fafb;
            --brand:#111827;
            --good:#047857;
            --warn:#b45309;
            --bad:#b91c1c;
        }

        *{ box-sizing:border-box; }
        html,body{ height:100%; }
        body{
            margin:0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
            color:var(--text);
            background: #f3f4f6; /* screen only */
        }

        .page{
            max-width: 980px;
            margin: 18px auto;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            overflow: hidden;
        }

        header{
            padding: 18px 22px;
            background: linear-gradient(135deg, #111827 0%, #1f2937 70%, #111827 100%);
            color: #fff;
        }
        .header-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
        }
        .title{
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .2px;
            margin: 0;
        }
        .subtitle{
            margin: 6px 0 0;
            font-size: 12px;
            opacity: .9;
            line-height: 1.4;
        }
        .meta{
            text-align:right;
            font-size: 11px;
            opacity: .9;
            line-height: 1.45;
            white-space: nowrap;
        }

        .content{
            padding: 18px 22px 12px;
        }

        .cards{
            display:grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }
        .card{
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            background: var(--bg);
        }
        .card-label{
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 700;
        }
        .card-value{
            margin-top: 6px;
            font-size: 16px;
            font-weight: 800;
        }
        .card-sub{
            margin-top: 4px;
            font-size: 11px;
            color: var(--muted);
        }

        .section{
            margin-top: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg);
        }
        .section-header{
            padding: 10px 12px;
            background: var(--soft);
            border-bottom: 1px solid var(--border);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
        }
        .section-title{
            font-weight: 800;
            font-size: 12px;
            margin: 0;
        }
        .section-note{
            font-size: 11px;
            color: var(--muted);
        }
        .section-body{
            padding: 10px 12px;
        }

        .filters{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 16px;
            font-size: 12px;
        }
        .kv{
            display:flex;
            justify-content:space-between;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px dashed var(--border);
        }
        .kv:last-child{ border-bottom: 0; }
        .k{ color: var(--muted); }
        .v{ font-weight: 700; color: var(--text); text-align:right; }

        table{
            width:100%;
            border-collapse: collapse;
        }
        thead th{
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted);
            font-weight: 800;
            background: var(--soft);
            border-bottom: 1px solid var(--border);
            padding: 9px 10px;
            text-align: left;
        }
        tbody td{
            padding: 9px 10px;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
        }
        tbody tr:last-child td{ border-bottom:0; }
        .num{ text-align:right; font-variant-numeric: tabular-nums; }
        .badge{
            display:inline-flex;
            align-items:center;
            gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            border: 1px solid var(--border);
            white-space: nowrap;
        }
        .badge.good{ color: var(--good); border-color: rgba(4,120,87,.25); background: rgba(4,120,87,.06); }
        .badge.warn{ color: var(--warn); border-color: rgba(180,83,9,.25); background: rgba(180,83,9,.06); }
        .badge.bad{ color: var(--bad); border-color: rgba(185,28,28,.25); background: rgba(185,28,28,.06); }

        .footer{
            padding: 10px 22px 16px;
            color: var(--muted);
            font-size: 11px;
            display:flex;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid var(--border);
        }

        .actions{
            display:flex;
            gap: 8px;
            justify-content:flex-end;
            padding: 12px 22px 0;
        }
        .btn{
            appearance:none;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text);
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
        }
        .btn.primary{
            background: var(--brand);
            color:#fff;
            border-color: rgba(255,255,255,.12);
        }

        @media (max-width: 900px){
            .page{ margin: 0; border-radius: 0; border-left:0; border-right:0; }
            .cards{ grid-template-columns: 1fr 1fr; }
            .filters{ grid-template-columns: 1fr; }
            .meta{ white-space: normal; }
        }

        @media print {
            @page { size: A4; margin: 12mm; }

            body{
                background: #fff !important;
            }
            .page{
                max-width: none !important;
                margin: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .actions{ display:none !important; }
            a[href]:after{ content:""; }
        }
    </style>
</head>
<body>
@php
    $rate = (float) ($summary['success_rate'] ?? 0);
    $rateClass = $rate >= 70 ? 'good' : ($rate >= 40 ? 'warn' : 'bad');

    $groupBy = $filters['group_by'] ?? 'method';
    $groupHeader = match($groupBy) {
        'month' => 'Month',
        'technician' => 'Technician',
        'bull' => 'Bull',
        default => 'Method',
    };

    $method = $filters['method'] ?? 'all';
    $methodLabel = match($method) {
        'ai' => 'Artificial Insemination (AI)',
        'natural_mating' => 'Natural Mating',
        default => 'All methods',
    };
@endphp

<div class="page">
    <div class="actions">
        <button class="btn" type="button" onclick="window.close()">Close</button>
        <button class="btn primary" type="button" onclick="window.print()">Print</button>
    </div>

    <header>
        <div class="header-top">
            <div>
                <h1 class="title">AI vs Natural Breeding Success</h1>
                <p class="subtitle">
                    Compare conception performance between Artificial Insemination (AI) and Natural Mating.
                    <br>
                    <span style="opacity:.92">(Confirmed Pregnancies ÷ Total Services) × 100</span>
                </p>
            </div>
            <div class="meta">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Date range:</strong> {{ $summary['from'] ?? '—' }} → {{ $summary['to'] ?? '—' }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="cards">
            <div class="card">
                <div class="card-label">Total services</div>
                <div class="card-value">{{ $summary['total_services'] ?? 0 }}</div>
                <div class="card-sub">All included breeding events</div>
            </div>
            <div class="card">
                <div class="card-label">Confirmed pregnancies</div>
                <div class="card-value">{{ $summary['confirmed_pregnancies'] ?? 0 }}</div>
                <div class="card-sub">With confirmed date</div>
            </div>
            <div class="card">
                <div class="card-label">Success rate</div>
                <div class="card-value">
                    <span class="badge {{ $rateClass }}">{{ number_format($rate, 2) }}%</span>
                </div>
                <div class="card-sub">Higher is better</div>
            </div>
            <div class="card">
                <div class="card-label">Services per conception (SPC)</div>
                <div class="card-value">{{ $summary['services_per_conception'] ?? '—' }}</div>
                <div class="card-sub">Lower is better</div>
            </div>
        </div>

        <div class="section" style="margin-top: 12px;">
            <div class="section-header">
                <h2 class="section-title">Filters</h2>
                <div class="section-note">This print view uses the same filters as the report page.</div>
            </div>
            <div class="section-body">
                <div class="filters">
                    <div class="kv"><div class="k">From</div><div class="v">{{ $filters['from'] ?? '—' }}</div></div>
                    <div class="kv"><div class="k">To</div><div class="v">{{ $filters['to'] ?? '—' }}</div></div>
                    <div class="kv"><div class="k">Method</div><div class="v">{{ $methodLabel }}</div></div>
                    <div class="kv"><div class="k">Group by</div><div class="v">{{ $groupHeader }}</div></div>
                    <div class="kv">
                        <div class="k">Animal</div>
                        <div class="v">
                            @if(!empty($animal))
                                {{ $animal->tag_number }}{{ $animal->name ? ' - '.$animal->name : '' }}
                            @else
                                All animals
                            @endif
                        </div>
                    </div>
                    <div class="kv"><div class="k">Success formula</div><div class="v">(Confirmed ÷ Services) × 100</div></div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Results</h2>
                <div class="section-note">Grouped summary table</div>
            </div>
            <div class="section-body" style="padding: 0;">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40%;">{{ $groupHeader }}</th>
                            <th class="num" style="width: 15%;">Services</th>
                            <th class="num" style="width: 15%;">Confirmed</th>
                            <th class="num" style="width: 15%;">Success rate</th>
                            <th class="num" style="width: 15%;">SPC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rows ?? []) as $r)
                            @php
                                $sr = (float) ($r['success_rate'] ?? 0);
                                $srClass = $sr >= 70 ? 'good' : ($sr >= 40 ? 'warn' : 'bad');
                            @endphp
                            <tr>
                                <td>{{ $r['group'] ?? '—' }}</td>
                                <td class="num">{{ $r['services'] ?? 0 }}</td>
                                <td class="num">{{ $r['confirmed'] ?? 0 }}</td>
                                <td class="num"><span class="badge {{ $srClass }}">{{ number_format($sr, 2) }}%</span></td>
                                <td class="num">{{ $r['services_per_conception'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 14px 10px; color: var(--muted); text-align: center;">
                                    No services found for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2 class="section-title">Interpretation notes</h2>
                <div class="section-note">For quick operational decisions</div>
            </div>
            <div class="section-body" style="font-size: 12px; color: var(--text); line-height: 1.55;">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px 18px;">
                    <div>
                        <div style="font-weight: 800; margin-bottom: 4px;">Success Rate (%)</div>
                        <div>
                            Higher values indicate better reproduction efficiency. Low success rate may indicate heat detection issues, technician skill gaps, bull fertility issues, or nutrition/health constraints.
                        </div>
                    </div>
                    <div>
                        <div style="font-weight: 800; margin-bottom: 4px;">SPC (Services Per Conception)</div>
                        <div>
                            Lower is better. High SPC increases costs and delays calving and milk income.
                        </div>
                    </div>
                </div>

                @if(!empty($summary['benchmarks']))
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed var(--border);">
                        <div style="font-weight: 800; margin-bottom: 6px;">Industry benchmarks (reference)</div>
                        <div style="display:flex; gap: 16px; flex-wrap:wrap; color: var(--muted);">
                            <div><strong style="color:var(--text);">AI:</strong> {{ $summary['benchmarks']['ai']['min'] ?? '—' }}–{{ $summary['benchmarks']['ai']['max'] ?? '—' }}%</div>
                            <div><strong style="color:var(--text);">Natural:</strong> {{ $summary['benchmarks']['natural_mating']['min'] ?? '—' }}–{{ $summary['benchmarks']['natural_mating']['max'] ?? '—' }}%</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer">
        <div>
            <div><strong>Report:</strong> AI vs Natural Breeding Success</div>
            <div style="margin-top:2px;">This document is generated by the system for printing and record keeping.</div>
        </div>
        <div style="text-align:right;">
            <div><strong>Printed:</strong> <span id="printedAt">{{ now()->toDateTimeString() }}</span></div>
            <div style="margin-top:2px;">Page <span class="pageNumber"></span> / <span class="totalPages"></span></div>
        </div>
    </div>
</div>

<script>
    // Keep it simple; browsers handle pagination differently.
    // Still, update "Printed" time for the current client.
    (function () {
        try {
            const el = document.getElementById('printedAt');
            if (el) el.textContent = new Date().toLocaleString();
        } catch (e) {}
    })();
</script>
</body>
</html>
