<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCollectionsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeRoles($request);

        // Filters
        $month = (string) $request->query('month', Carbon::now()->format('Y-m'));
        $farmId = $request->query('farm_id');

        try {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $monthStart = Carbon::now()->startOfMonth();
            $month = $monthStart->format('Y-m');
        }
        $monthEnd = (clone $monthStart)->endOfMonth();

        // Farm dropdown
        $farms = Farm::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(fn(Farm $f) => ['id' => $f->id, 'name' => $f->name]);

        // Invoice-wise collection for the month
        // - Online payments: show succeeded SubscriptionPayment rows (money received)
        // - COD subscriptions: there may be NO payment row yet, so show the invoice itself
        //   (so admin can see invoice-wise "collection" pipeline even when payment is pending)
        $paymentsQuery = SubscriptionPayment::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'succeeded');

        if ($farmId) {
            $paymentsQuery->where('farm_id', $farmId);
        }

        $invoiceWiseFromPayments = $paymentsQuery
            ->with([
                'invoice' => function ($q) {
                    $q->select(['id', 'invoice_number', 'farm_id', 'invoice_date', 'total_cents', 'status']);
                },
                'farm' => function ($q) {
                    $q->select(['id', 'name']);
                },
            ])
            ->orderByDesc('created_at')
            ->limit(500)
            ->get()
            ->map(function ($p) {
                return [
                    'type' => 'payment',
                    'id' => $p->id,
                    'paid_at' => optional($p->created_at)->toDateTimeString(),
                    'amount_cents' => (int) $p->amount_cents,
                    'status' => (string) $p->status,
                    'farm' => $p->farm ? [
                        'id' => $p->farm->id,
                        'name' => $p->farm->name,
                    ] : null,
                    'invoice' => $p->invoice ? [
                        'id' => $p->invoice->id,
                        'invoice_number' => $p->invoice->invoice_number ?? (string) $p->invoice->id,
                        'invoice_date' => optional($p->invoice->invoice_date)->toDateString(),
                        'total_cents' => (int) $p->invoice->total_cents,
                        'status' => (string) $p->invoice->status,
                    ] : null,
                ];
            });

        // COD invoices: invoices in the month that have no succeeded payment yet
        $codInvoicesQuery = SubscriptionInvoice::query()
            ->whereBetween('invoice_date', [$monthStart, $monthEnd])
            ->whereIn('status', ['unpaid', 'pending'])
            ->when($farmId, fn($q) => $q->where('farm_id', $farmId))
            ->whereDoesntHave('payments', function ($q) {
                $q->where('status', 'succeeded');
            })
            ->with(['farm:id,name'])
            ->orderByDesc('invoice_date')
            ->limit(500)
            ->get()
            ->map(function ($inv) {
                return [
                    'type' => 'invoice',
                    'id' => $inv->id,
                    'paid_at' => null,
                    'amount_cents' => (int) $inv->total_cents,
                    'status' => (string) $inv->status,
                    'farm' => $inv->farm ? [
                        'id' => $inv->farm->id,
                        'name' => $inv->farm->name,
                    ] : null,
                    'invoice' => [
                        'id' => $inv->id,
                        'invoice_number' => $inv->invoice_number ?? (string) $inv->id,
                        'invoice_date' => optional($inv->invoice_date)->toDateString(),
                        'total_cents' => (int) $inv->total_cents,
                        'status' => (string) $inv->status,
                    ],
                ];
            });

        $invoiceWise = $invoiceWiseFromPayments
            ->concat($codInvoicesQuery)
            ->sortByDesc(function ($row) {
                return $row['paid_at'] ?? $row['invoice']['invoice_date'] ?? '';
            })
            ->values();

        // Farm-wise collection summary
        $farmWise = SubscriptionPayment::query()
            ->selectRaw('farm_id, SUM(amount_cents) as total_cents, COUNT(*) as payments_count')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'succeeded')
            ->when($farmId, fn($q) => $q->where('farm_id', $farmId))
            ->groupBy('farm_id')
            ->with(['farm:id,name'])
            ->get()
            ->map(function ($row) {
                return [
                    'farm' => $row->farm ? [
                        'id' => $row->farm->id,
                        'name' => $row->farm->name,
                    ] : ['id' => $row->farm_id, 'name' => 'Unknown'],
                    'total_cents' => (int) $row->total_cents,
                    'payments_count' => (int) $row->payments_count,
                ];
            })
            ->sortByDesc('total_cents')
            ->values();

        // Totals
        $totalCollectionCents = SubscriptionPayment::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'succeeded')
            ->when($farmId, fn($q) => $q->where('farm_id', $farmId))
            ->sum('amount_cents');

        $totalReceivableCents = SubscriptionInvoice::query()
            ->whereBetween('invoice_date', [$monthStart, $monthEnd])
            ->whereIn('status', ['unpaid', 'pending'])
            ->when($farmId, fn($q) => $q->where('farm_id', $farmId))
            ->sum('total_cents');

        return Inertia::render('Admin/Collections', [
            'filters' => [
                'month' => $month,
                'farm_id' => $farmId ? (int) $farmId : null,
            ],
            'kpis' => [
                'this_month_receivable_cents' => (int) $totalReceivableCents,
                'this_month_collection_cents' => (int) $totalCollectionCents,
                'month_start' => $monthStart->toDateString(),
                'month_end' => $monthEnd->toDateString(),
            ],
            'farms' => $farms,
            'invoice_wise' => $invoiceWise,
            'farm_wise' => $farmWise,
        ]);
    }

    public function collectInvoice(Request $request, SubscriptionInvoice $invoice)
    {
        $this->authorizeRoles($request);

        // Only allow collecting invoices that are not already collected
        if (!in_array($invoice->status, ['unpaid', 'pending'], true)) {
            return back()->with('error', 'Invoice is not collectable.');
        }

        // If already has a succeeded payment, do nothing (idempotent)
        $alreadyCollected = $invoice->payments()
            ->where('status', 'succeeded')
            ->exists();

        if ($alreadyCollected) {
            return back()->with('success', 'Invoice already collected.');
        }

        // Create a "manual/COD" succeeded payment entry so it appears in collections
        SubscriptionPayment::create([
            'farm_id' => $invoice->farm_id,
            'subscription_invoice_id' => $invoice->id,
            'gateway' => 'cod',
            'amount_cents' => (int) $invoice->total_cents,
            'currency' => $invoice->currency ?? 'BDT',
            'status' => 'succeeded',
            'provider_payment_id' => null,
            'provider_payload' => [
                'collected_by_admin_id' => optional($request->user())->id,
                'collected_at' => now()->toDateTimeString(),
            ],
        ]);

        // Mark invoice as paid
        $invoice->status = 'paid';
        $invoice->save();

        return back()->with('success', 'Invoice collected successfully.');
    }

    private function authorizeRoles(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->hasRole('Super Admin') || $user->hasRole('admin')), 403);
    }
}
