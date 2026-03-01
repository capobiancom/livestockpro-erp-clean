<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SupplierPaymentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SupplierPayment::class, 'supplier_payment');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $q = $request->input('q');

        $farmId = $user->farm_id;

        // Fetch Purchases with their payments
        $purchases = Purchase::with(['supplier:id,name', 'supplierPayments' => function ($query) use ($q) {
            $query->when($q, fn($qb) => $qb->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%$q%")));
        }])
            ->whereHas('supplierPayments', function ($query) use ($farmId, $q) {
                $query->where('farm_id', $farmId)
                    ->when($q, fn($qb) => $qb->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%$q%")));
            })
            ->when($user->hasRole('farm owner'), function ($query) use ($farmId) {
                $query->where('farm_id', $farmId);
            })
            ->latest('created_at')
            ->get();

        // Add type attribute
        $purchases = $purchases->map(function ($purchase) {
            $purchase->type = Purchase::class;
            return $purchase;
        });

        // Paginate the results
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $purchases->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedInvoices = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $purchases->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        // Statistics calculation
        $baseQuery = SupplierPayment::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->when($q, fn($qb) => $qb->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%$q%")));

        $statistics = [
            'total_payments' => (clone $baseQuery)->count(),
            'total_amount_paid' => (clone $baseQuery)->sum('amount'),
            'this_month_payments' => (clone $baseQuery)->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
            'this_year_payments' => (clone $baseQuery)->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ];

        return Inertia::render('SupplierPayments/Index', [
            'invoices' => $paginatedInvoices,
            'filters' => $request->only('q'),
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $purchases = Purchase::select('id', 'invoice_number', 'supplier_id', 'total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_amount - COALESCE(paid_amount, 0)) > 0')
            ->with('supplier:id,name')
            ->get();

        $payables = $purchases->map(function ($purchase) {
            $purchase->type = 'Purchase';
            $dueAmount = $purchase->total_amount - ($purchase->paid_amount ?? 0);
            $purchase->display_name = "Purchase Invoice: {$purchase->invoice_number} (Due: " . number_format($dueAmount, 2) . ")";
            return $purchase;
        });

        $paymentMethods = ['Cash', 'Bank Transfer', 'Mobile Banking', 'Cheque'];

        return Inertia::render('SupplierPayments/Create', [
            'suppliers' => $suppliers,
            'payables' => $payables,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'payable_id' => ['required', 'integer'],
            'payable_type' => ['required', 'string'],
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => ['required', Rule::in(['Cash', 'Bank Transfer', 'Mobile Banking', 'Cheque'])],
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;
        $validated['farm_id'] = $user->farm_id;
        $validated['reference_number'] = $validated['reference_number'] ?? SupplierPayment::generateReferenceNumber();

        // Extract payable_id and payable_type
        $payableId = $validated['payable_id'];
        $payableType = $validated['payable_type'];

        // Remove payable_id and payable_type from validated array
        unset($validated['payable_id']);
        unset($validated['payable_type']);

        $checkSale = Purchase::where('id', $payableId)->first();

        $paidToAmount = $validated['amount'];
        $remainingAmount = $checkSale->total_amount - $checkSale->paid_amount;

        if ($remainingAmount < $paidToAmount) {
            return back()->withErrors(['error' => 'Paid amount cannot be greater than total amount.'])->withInput();
        }

        // Create the supplier payment
        $supplierPayment = SupplierPayment::create(array_merge($validated, [
            'purchase_source_id' => $payableId,
            'purchase_source_type' => $payableType,
        ]));

        // Update the paid_amount of the associated purchase
        $payable = $supplierPayment->purchase_source;
        if ($payable) {
            $payable->increment('paid_amount', $supplierPayment->amount);
        }

        return redirect()->route('supplier-payments.index')->with('success', 'Supplier payment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load(['supplier', 'purchase_source']);
        return Inertia::render('SupplierPayments/Show', ['supplierPayment' => $supplierPayment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $suppliers = Supplier::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $purchases = Purchase::select('id', 'invoice_number', 'supplier_id', 'total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_amount - COALESCE(paid_amount, 0)) > 0')
            ->orWhere('id', $supplierPayment->purchase_source_id)
            ->with('supplier:id,name')
            ->get();

        $payables = $purchases->map(function ($purchase) {
            $purchase->type = 'Purchase';
            $dueAmount = $purchase->total_amount - ($purchase->paid_amount ?? 0);
            $purchase->display_name = "Purchase Invoice: {$purchase->invoice_number} (Due: " . number_format($dueAmount, 2) . ")";
            return $purchase;
        });

        $paymentMethods = ['Cash', 'Bank Transfer', 'Mobile Banking', 'Cheque'];

        $supplierPayment->load('purchase_source');

        return Inertia::render('SupplierPayments/Edit', [
            'supplierPayment' => array_merge($supplierPayment->toArray(), [
                'payment_date' => $supplierPayment->payment_date->format('Y-m-d'),
                'payable_id' => $supplierPayment->purchase_source_id,
                'payable_type' => $supplierPayment->purchase_source_type,
            ]),
            'suppliers' => $suppliers,
            'payables' => $payables,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'payable_id' => ['required', 'integer'],
            'payable_type' => ['required', 'string'],
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => ['required', Rule::in(['Cash', 'Bank Transfer', 'Mobile Banking', 'Cheque'])],
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $user->id;
        $validated['farm_id'] = $user->farm_id;

        // Get the original amount and payable before updating
        $originalAmount = $supplierPayment->amount;
        $originalPayable = $supplierPayment->purchase_source;

        $paidToAmount = $validated['amount'];
        $remainingAmount = $originalPayable->total_amount - $originalPayable->paid_amount;



        if ($remainingAmount < $paidToAmount) {
            return back()->withErrors(['error' => 'Paid amount cannot be greater than total amount.'])->withInput();
        }


        // Extract payable_id and payable_type
        $payableId = $validated['payable_id'];
        $payableType = $validated['payable_type'];

        // Remove payable_id and payable_type from validated array
        unset($validated['payable_id']);
        unset($validated['payable_type']);

        // Update the supplier payment
        $supplierPayment->update(array_merge($validated, [
            'purchase_source_id' => $payableId,
            'purchase_source_type' => $payableType,
        ]));

        // Update the paid_amount of the original payable
        if ($originalPayable) {
            $originalPayable->decrement('paid_amount', $originalAmount);
        }

        // Update the paid_amount of the new payable
        $newPayable = $supplierPayment->purchase_source;
        if ($newPayable) {
            $newPayable->increment('paid_amount', $supplierPayment->amount);
        }

        return redirect()->route('supplier-payments.index')->with('success', 'Supplier payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        // Decrement the paid_amount of the associated payable before deleting
        $payable = $supplierPayment->purchase_source;
        if ($payable) {
            $payable->decrement('paid_amount', $supplierPayment->amount);
        }

        $supplierPayment->delete();
        return redirect()->route('supplier-payments.index')->with('success', 'Supplier payment deleted successfully.');
    }
}
