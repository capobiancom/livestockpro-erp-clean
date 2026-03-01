<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SaleTransaction;
use App\Models\Sale;
use App\Models\MilkSale;
use App\Http\Requests\StoreCustomerPaymentRequest;
use App\Http\Requests\UpdateCustomerPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomerPaymentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SaleTransaction::class, 'customer_payment');
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

        // Fetch Sales with their payments
        $sales = Sale::with(['customer:id,name', 'saleTransactions' => function ($query) use ($q) {
            $query->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")));
        }])
            ->whereHas('saleTransactions', function ($query) use ($farmId, $q) {
                $query->where('farm_id', $farmId)
                    ->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")));
            })
            ->when($user->hasRole('farm owner'), function ($query) use ($farmId) {
                $query->where('farm_id', $farmId);
            })
            ->latest('created_at')
            ->get();

        // Fetch MilkSales with their payments
        $milkSales = MilkSale::with(['customer:id,name', 'saleTransactions' => function ($query) use ($q) {
            $query->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")));
        }])
            ->whereHas('saleTransactions', function ($query) use ($farmId, $q) {
                $query->where('farm_id', $farmId)
                    ->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")));
            })
            ->when($user->hasRole('farm owner'), function ($query) use ($farmId) {
                $query->where('farm_id', $farmId);
            })
            ->latest('created_at')
            ->get();

        // Add type attribute to differentiate between Sale and MilkSale
        $sales = $sales->map(function ($sale) {
            $sale->type = Sale::class;
            return $sale;
        });

        $milkSales = $milkSales->map(function ($milkSale) {
            $milkSale->type = MilkSale::class;
            return $milkSale;
        });

        // Merge and paginate the results
        $invoices = $sales->merge($milkSales)->sortByDesc('created_at');

        // Manually paginate the collection
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $invoices->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedInvoices = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, $invoices->count(), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        // Statistics calculation (still based on SaleTransaction)
        $baseQuery = SaleTransaction::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->when($q, fn($qb) => $qb->whereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%")));

        $statistics = [
            'total_payments' => (clone $baseQuery)->count(),
            'total_amount_paid' => (clone $baseQuery)->sum('amount'),
            'this_month_payments' => (clone $baseQuery)->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),
            'this_year_payments' => (clone $baseQuery)->whereYear('transaction_date', now()->year)
                ->sum('amount'),
        ];

        return Inertia::render('CustomerPayments/Index', [
            'invoices' => $paginatedInvoices, // Changed from customerPayments to invoices
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
        $customers = Customer::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $sales = Sale::select('id', 'invoice_number', 'customer_id', 'total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_amount - paid_amount) > 0') // Only show sales with outstanding due amounts
            ->with('customer:id,name')
            ->get();

        $milkSales = MilkSale::select('id', 'invoice_number', 'customer_id', 'total_price as total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_price - paid_amount) > 0') // Only show milk sales with outstanding due amounts
            ->with('customer:id,name')
            ->get();

        $payables = $sales->map(function ($sale) {
            $sale->type = 'Sale';
            $sale->display_name = "Sale Invoice: {$sale->invoice_number} (Due: " . ($sale->total_amount - $sale->paid_amount) . ")";
            return $sale;
        })->merge($milkSales->map(function ($milkSale) {
            $milkSale->type = 'MilkSale';
            $milkSale->display_name = "Milk Sale Invoice: {$milkSale->invoice_number} (Due: " . ($milkSale->total_amount - $milkSale->paid_amount) . ")";
            return $milkSale;
        }));

        $paymentMethods = ['Cash', 'Bank Transfer', 'Mobile Banking'];

        return Inertia::render('CustomerPayments/Create', [
            'customers' => $customers,
            'payables' => $payables,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerPaymentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;
        $validated['farm_id'] = Auth::user()->farm_id;
        $validated['reference_number'] = SaleTransaction::generateReferenceNumber();

        return DB::transaction(function () use ($validated) {
            // Extract payable_id and payable_type from the request
            $payableId = $validated['payable_id'];
            $payableType = $validated['payable_type']; // 'App\Models\Sale' or 'App\Models\MilkSale'

            // Remove payable_id and payable_type from validated array before creating SaleTransaction
            unset($validated['payable_id'], $validated['payable_type']);

            if ($payableType === 'App\Models\Sale') {
                $checkSale = Sale::where('id', $payableId)->first();

                if (!$checkSale) {
                    return back()->withErrors(['error' => 'Selected invoice not found.'])->withInput();
                }

                $paidToAmount = (float) $validated['amount'];
                $remainingAmount = (float) $checkSale->total_amount - (float) $checkSale->paid_amount;

                if ($remainingAmount < $paidToAmount) {
                    return back()->withErrors(['error' => 'Paid amount cannot be greater than total amount.'])->withInput();
                }
            }

            if ($payableType === 'App\Models\MilkSale') {
                $checkSale = MilkSale::where('id', $payableId)->first();

                if (!$checkSale) {
                    return back()->withErrors(['error' => 'Selected invoice not found.'])->withInput();
                }

                $paidToAmount = (float) $validated['amount'];
                $remainingAmount = (float) $checkSale->total_price - (float) $checkSale->paid_amount;

                if ($remainingAmount < $paidToAmount) {
                    return back()->withErrors(['error' => 'Paid amount cannot be greater than total amount.'])->withInput();
                }
            }

            $customerPayment = SaleTransaction::create(array_merge($validated, [
                'sale_transaction_source_id' => $payableId,
                'sale_transaction_source_type' => $payableType,
            ]));

            // Update the paid_amount of the associated payable
            $payable = $customerPayment->sale_transaction_source;

            if ($payable) {
                $payable->increment('paid_amount', $customerPayment->amount);
            }

            return redirect()->route('customer-payments.index')->with('success', 'Customer payment recorded successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleTransaction $customerPayment)
    {
        $customerPayment->load(['customer', 'sale_transaction_source']);
        return Inertia::render('CustomerPayments/Show', ['customerPayment' => $customerPayment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleTransaction $customerPayment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $customers = Customer::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();

        $sales = Sale::select('id', 'invoice_number', 'customer_id', 'total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_amount - paid_amount) > 0') // Only show sales with outstanding due amounts
            ->orWhere('id', $customerPayment->sale_transaction_source_id) // Include the current sale even if due amount is 0
            ->with('customer:id,name')
            ->get();

        $milkSales = MilkSale::select('id', 'invoice_number', 'customer_id', 'total_price as total_amount', 'paid_amount')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->whereRaw('(total_price - paid_amount) > 0') // Only show milk sales with outstanding due amounts
            ->orWhere('id', $customerPayment->sale_transaction_source_id) // Include the current milk sale even if due amount is 0
            ->with('customer:id,name')
            ->get();

        $payables = $sales->map(function ($sale) {
            $sale->type = 'Sale';
            $sale->display_name = "Sale Invoice: {$sale->invoice_number} (Due: " . ($sale->total_amount - $sale->paid_amount) . ")";
            return $sale;
        })->merge($milkSales->map(function ($milkSale) {
            $milkSale->type = 'MilkSale';
            $milkSale->display_name = "Milk Sale Invoice: {$milkSale->invoice_number} (Due: " . ($milkSale->total_amount - $milkSale->paid_amount) . ")";
            return $milkSale;
        }));

        $paymentMethods = ['Cash', 'Bank Transfer', 'Mobile Banking'];

        $customerPayment->load('sale_transaction_source');

        return Inertia::render('CustomerPayments/Edit', [
            'customerPayment' => array_merge($customerPayment->toArray(), [
                'transaction_date' => $customerPayment->transaction_date->format('Y-m-d'),
                'payable_id' => $customerPayment->sale_transaction_source_id,
                'payable_type' => $customerPayment->sale_transaction_source_type,
            ]),
            'customers' => $customers,
            'payables' => $payables,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerPaymentRequest $request, SaleTransaction $customerPayment)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::user()->id;
        $validated['farm_id'] = Auth::user()->farm_id;

        return DB::transaction(function () use ($validated, $customerPayment) {
            // Original state
            $originalAmount = (float) $customerPayment->amount;
            $originalPayableId = $customerPayment->sale_transaction_source_id;
            $originalPayableType = $customerPayment->sale_transaction_source_type;
            $originalPayable = $customerPayment->sale_transaction_source;

            // Requested new state
            $newAmount = (float) $validated['amount'];
            $newPayableId = $validated['payable_id'];
            $newPayableType = $validated['payable_type'];

            // Remove payable_id and payable_type from validated array before updating SaleTransaction
            unset($validated['payable_id'], $validated['payable_type']);

            // If payable changed, we need to validate against the NEW payable's remaining due
            $newPayable = ($newPayableId == $originalPayableId && $newPayableType == $originalPayableType)
                ? $originalPayable
                : ($newPayableType === Sale::class
                    ? Sale::find($newPayableId)
                    : MilkSale::find($newPayableId));

            if (!$newPayable) {
                return back()->withErrors(['error' => 'Selected invoice not found.'])->withInput();
            }

            // Remaining due should consider that we are editing an existing payment:
            // available = total - (paid_amount - originalAmount)
            $total = (float) ($newPayable->total_amount ?? $newPayable->total_price);
            $paid = (float) $newPayable->paid_amount;
            $available = $total - ($paid - (($newPayableId == $originalPayableId && $newPayableType == $originalPayableType) ? $originalAmount : 0.0));

            if ($newAmount > $available) {
                return back()->withErrors(['error' => 'Paid amount cannot be greater than total amount.'])->withInput();
            }

            // Update the payment record itself
            $customerPayment->update(array_merge($validated, [
                'sale_transaction_source_id' => $newPayableId,
                'sale_transaction_source_type' => $newPayableType,
            ]));

            // Adjust paid_amount(s) using delta logic
            if ($originalPayable && !($newPayableId == $originalPayableId && $newPayableType == $originalPayableType)) {
                // moved to a different invoice: remove full original from old payable
                $originalPayable->decrement('paid_amount', $originalAmount);
                $newPayable->increment('paid_amount', $newAmount);
            } else {
                // same invoice: apply only the difference (this matches "Amount field + existing sale_transactions.amount")
                $delta = $newAmount - $originalAmount;
                if ($delta > 0) {
                    $newPayable->increment('paid_amount', $delta);
                } elseif ($delta < 0) {
                    $newPayable->decrement('paid_amount', abs($delta));
                }
            }

            return redirect()->route('customer-payments.index')->with('success', 'Customer payment updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleTransaction $customerPayment)
    {
        // Decrement the paid_amount of the associated payable before deleting the payment
        $payable = $customerPayment->sale_transaction_source;
        if ($payable) {
            $payable->decrement('paid_amount', $customerPayment->amount);
        }

        $customerPayment->delete();
        return redirect()->route('customer-payments.index')->with('success', 'Customer payment deleted successfully.');
    }
}
