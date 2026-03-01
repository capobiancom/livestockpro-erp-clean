<?php

namespace App\Http\Controllers;

use App\Models\MilkSale;
use App\Models\Customer; // Changed from Supplier to Customer
use App\Models\Farm; // Import Farm model
use App\Models\MilkRecord;
use App\Models\Setting; // Import Setting model
use App\Models\SaleTransaction; // Import SaleTransaction model
use App\Enums\SaleStatus; // Import SaleStatus Enum
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\DB; // Import DB facade for transactions
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\User; // Import User model for type hinting

class MilkSaleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(MilkSale::class, 'milk_sale');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $q = $request->input('q');
        $milkSales = MilkSale::with(['customer'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->where('invoice_number', 'like', "%$q%")
                ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%$q%"))) // This will now correctly query the Customer model
            ->latest('sale_date')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics
        $baseQuery = MilkSale::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        });

        $statistics = [
            'total_sales' => (clone $baseQuery)->count(),
            'total_quantity' => (clone $baseQuery)->sum('quantity'),
            'total_revenue' => (clone $baseQuery)->sum('total_price'),
            'this_month_revenue' => (clone $baseQuery)->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('total_price'),
            'this_month_quantity' => (clone $baseQuery)->whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('quantity'),
            'avg_price_per_liter' => (clone $baseQuery)->avg('unit_price'),
        ];

        return Inertia::render('MilkSales/Index', [
            'milkSales' => $milkSales,
            'statistics' => $statistics,
            'filters' => $request->only('q'),
            'currency' => Setting::first()->currency ?? 'BDT', // Fetch currency from settings, default to 'BDT'
        ]);
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $customers = Customer::select('id', 'name', 'contact_person')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $saleStatuses = collect(SaleStatus::cases())->map(function ($status) {
            return ['value' => $status->value, 'name' => $status->name];
        })->toArray();

        return Inertia::render('MilkSales/Create', [
            'customers' => $customers,
            'farms' => $farms,
            'saleStatuses' => $saleStatuses,
        ]);
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'invoice_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('milk_sales', 'invoice_number')
                    ->where(fn($q) => $q->where('farm_id', $user->farm_id)),
            ],
            'customer_id' => 'required|exists:customers,id', // Changed from suppliers to customers
            'sale_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50|in:liters',
            'unit_price' => 'required|numeric|min:0',
            'paid_amount' => ['required', 'numeric', 'min:0', 'lte:total_price'],
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,partial,paid',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                // Enforce farm_id for farm owners
                if ($user->hasRole('farm owner')) {
                    $validated['farm_id'] = $user->farm_id;
                    $validated['user_id'] = $user->id;
                }

                // Validate that the selected customer belongs to the user's farm
                if ($user->hasRole('farm owner') && !empty($validated['customer_id'])) {
                    $customer = Customer::where('id', $validated['customer_id']) // Changed from Supplier to Customer
                        ->where('farm_id', $user->farm_id)
                        ->first();
                    if (!$customer) {
                        throw new \Exception('The selected customer does not belong to your farm.');
                    }
                }

                // Milk stock validation (milk_records.quantity_liters - milk_sales.quantity)
                $farmId = $validated['farm_id'] ?? $user->farm_id;

                $totalProduced = MilkRecord::query()
                    ->where('farm_id', $farmId)
                    ->sum('quantity_liters');

                $totalSold = MilkSale::query()
                    ->where('farm_id', $farmId)
                    ->sum('quantity');

                $availableStock = (float) $totalProduced - (float) $totalSold;

                if ((float) $validated['quantity'] > $availableStock) {
                    throw new \Exception('Insufficient milk stock. Available: ' . number_format($availableStock, 2) . ' L');
                }

                if (!isset($validated['invoice_number']) || empty($validated['invoice_number'])) {
                    $validated['invoice_number'] = MilkSale::generateInvoiceNumber();
                }

                $milkSale = MilkSale::create($validated);

                // Auto journal integration for milk sales (Accounts Receivable vs Milk Sales)
                $arAccount = ChartOfAccount::query()
                    ->where('code', '1003')
                    ->first();

                if (!$arAccount) {
                    throw new \Exception('Chart of account 1003 (Accounts Receivable) not found.');
                }

                $milkSalesAccount = ChartOfAccount::query()
                    ->where('code', '4001')
                    ->first();

                if (!$milkSalesAccount) {
                    throw new \Exception('Chart of account 4001 (Milk Sales) not found.');
                }

                $entry = JournalEntry::query()->create([
                    'farm_id' => $milkSale->farm_id,
                    'user_id' => $milkSale->user_id,
                    'entry_date' => $milkSale->sale_date,
                    'reference_type' => 'milk_sale',
                    'reference_id' => $milkSale->id,
                    'description' => 'Milk sale invoice ' . $milkSale->invoice_number,
                    'status' => 'posted',
                    'created_by' => $milkSale->user_id,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_id' => $arAccount->id,
                        'debit_amount' => (float) $milkSale->total_price,
                        'credit_amount' => 0,
                        'narration' => 'Accounts Receivable',
                    ],
                    [
                        'account_id' => $milkSalesAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => (float) $milkSale->total_price,
                        'narration' => 'Milk Sales',
                    ],
                ]);

                if ($milkSale->paid_amount > 0) {
                    $milkSale->saleTransactions()->create([
                        'customer_id' => $milkSale->customer_id,
                        'sale_transaction_source_id' => $milkSale->id,
                        'sale_transaction_source_type' => MilkSale::class,
                        'transaction_date' => $milkSale->sale_date,
                        'amount' => $milkSale->paid_amount,
                        'payment_method' => 'Cash', // Default to Cash, can be made dynamic if needed
                        'reference_number' => SaleTransaction::generateReferenceNumber(),
                        'notes' => 'Payment received during milk sale creation.',
                        'farm_id' => $milkSale->farm_id,
                        'user_id' => $milkSale->user_id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('milk-sales.index')->with('success', 'Milk sale recorded successfully.');
    }

    public function show(MilkSale $milkSale)
    {
        $this->authorize('view', $milkSale);
        $milkSale->load(['customer', 'saleTransactions']); // This will now correctly load the Customer model
        return Inertia::render('MilkSales/Show', ['milkSale' => $milkSale]);
    }

    public function printInvoice(MilkSale $milkSale)
    {
        $this->authorize('view', $milkSale);
        $milkSale->load(['customer', 'farm', 'user']);
        return Inertia::render('MilkSales/PrintInvoice', ['milkSale' => $milkSale]);
    }

    public function edit(MilkSale $milkSale)
    {
        $this->authorize('update', $milkSale);
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        // Format the sale_date to Y-m-d format for HTML date input
        $milkSaleData = $milkSale->toArray();
        $milkSaleData['sale_date'] = $milkSale->sale_date ? $milkSale->sale_date->format('Y-m-d') : '';

        $customers = Customer::select('id', 'name', 'contact_person')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get();
        $farms = Farm::select('id', 'name')
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('id', $user->farm_id);
            })->get();

        $saleStatuses = collect(SaleStatus::cases())->map(function ($status) {
            return ['value' => $status->value, 'name' => $status->name];
        })->toArray();

        return Inertia::render('MilkSales/Edit', [
            'milkSale' => $milkSaleData,
            'customers' => $customers,
            'farms' => $farms,
            'saleStatuses' => $saleStatuses,
        ]);
    }

    public function update(Request $request, MilkSale $milkSale)
    {
        $this->authorize('update', $milkSale);
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $validated = $request->validate([
            'invoice_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('milk_sales', 'invoice_number')
                    ->ignore($milkSale->id)
                    ->where(fn($q) => $q->where('farm_id', $user->farm_id)),
            ],
            'customer_id' => 'required|exists:customers,id', // Changed from suppliers to customers
            'sale_date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50|in:liters',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0|lte:total_price',
            'status' => 'required|in:unpaid,partial,paid',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $user, $milkSale) {
                // Enforce farm_id for farm owners
                if ($user->hasRole('farm owner')) {
                    $validated['farm_id'] = $user->farm_id;
                }

                // Validate that the selected customer belongs to the user's farm
                if ($user->hasRole('farm owner') && !empty($validated['customer_id'])) {
                    $customer = Customer::where('id', $validated['customer_id']) // Changed from Supplier to Customer
                        ->where('farm_id', $user->farm_id)
                        ->first();
                    if (!$customer) {
                        throw new \Exception('The selected customer does not belong to your farm.');
                    }
                }

                // Milk stock validation (exclude current sale quantity)
                $farmId = $validated['farm_id'] ?? $milkSale->farm_id ?? $user->farm_id;

                $totalProduced = MilkRecord::query()
                    ->where('farm_id', $farmId)
                    ->sum('quantity_liters');

                $totalSoldExcludingThis = MilkSale::query()
                    ->where('farm_id', $farmId)
                    ->where('id', '!=', $milkSale->id)
                    ->sum('quantity');

                $availableStock = (float) $totalProduced - (float) $totalSoldExcludingThis;

                if ((float) $validated['quantity'] > $availableStock) {
                    throw new \Exception('Insufficient milk stock. Available: ' . number_format($availableStock, 2) . ' L');
                }

                if (!isset($validated['invoice_number']) || empty($validated['invoice_number'])) {
                    $validated['invoice_number'] = MilkSale::generateInvoiceNumber();
                }

                $oldTotalPrice = (float) $milkSale->total_price;

                // Update editable sale fields
                $milkSale->fill([
                    'invoice_number' => $validated['invoice_number'],
                    'customer_id' => $validated['customer_id'],
                    'sale_date' => $validated['sale_date'],
                    'quantity' => $validated['quantity'],
                    'unit' => $validated['unit'],
                    'unit_price' => $validated['unit_price'],
                    'total_price' => $validated['total_price'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Calculate total paid amount from associated sale transactions
                $totalPaidAmount = $milkSale->saleTransactions()->sum('amount');

                $paidAmount = $validated['paid_amount'];
                $remainingAmount = $milkSale->total_price - $milkSale->paid_amount;

                if ($milkSale->total_price == $totalPaidAmount) {
                    $milkSale->status = SaleStatus::Paid;
                    $milkSale->save();
                }

                if (floatVal($remainingAmount) < floatVal($paidAmount)) {
                    throw new \Exception('Paid amount cannot be greater than total price.');
                }

                $milkSale->paid_amount = ($totalPaidAmount + $paidAmount);

                // Determine status based on total_price and paid_amount
                if ($totalPaidAmount >= $milkSale->total_price) {
                    $milkSale->status = SaleStatus::Paid;
                } elseif ($totalPaidAmount > 0) {
                    $milkSale->status = SaleStatus::Partial;
                } else {
                    $milkSale->status = SaleStatus::Unpaid;
                }

                $milkSale->save();

                // Adjust journals based on total price increment/decrement
                $newTotalPrice = (float) $milkSale->total_price;
                $deltaTotalPrice = $newTotalPrice - $oldTotalPrice;

                if ($deltaTotalPrice != 0.0) {
                    $arAccount = ChartOfAccount::query()
                        ->where('code', '1003')
                        ->first();

                    if (!$arAccount) {
                        throw new \Exception('Chart of account 1003 (Accounts Receivable) not found.');
                    }

                    $milkSalesAccount = ChartOfAccount::query()
                        ->where('code', '4001')
                        ->first();

                    if (!$milkSalesAccount) {
                        throw new \Exception('Chart of account 4001 (Milk Sales) not found.');
                    }

                    $abs = abs($deltaTotalPrice);

                    $entry = JournalEntry::query()->create([
                        'farm_id' => $milkSale->farm_id,
                        'user_id' => $milkSale->user_id,
                        'entry_date' => $milkSale->sale_date,
                        'reference_type' => 'milk_sale',
                        'reference_id' => $milkSale->id,
                        'description' => 'Milk sale adjustment ' . $milkSale->invoice_number,
                        'status' => 'posted',
                        'created_by' => $milkSale->user_id,
                    ]);

                    if ($deltaTotalPrice > 0) {
                        // Increase: DR AR, CR Milk Sales
                        $entry->lines()->createMany([
                            [
                                'account_id' => $arAccount->id,
                                'debit_amount' => $abs,
                                'credit_amount' => 0,
                                'narration' => 'Accounts Receivable (Adjustment)',
                            ],
                            [
                                'account_id' => $milkSalesAccount->id,
                                'debit_amount' => 0,
                                'credit_amount' => $abs,
                                'narration' => 'Milk Sales (Adjustment)',
                            ],
                        ]);
                    } else {
                        // Decrease: DR Milk Sales, CR AR
                        $entry->lines()->createMany([
                            [
                                'account_id' => $milkSalesAccount->id,
                                'debit_amount' => $abs,
                                'credit_amount' => 0,
                                'narration' => 'Milk Sales (Adjustment)',
                            ],
                            [
                                'account_id' => $arAccount->id,
                                'debit_amount' => 0,
                                'credit_amount' => $abs,
                                'narration' => 'Accounts Receivable (Adjustment)',
                            ],
                        ]);
                    }
                }

                if ($paidAmount > 0) {
                    $milkSale->saleTransactions()->create([
                        'sale_transaction_source_id' => $milkSale->id, // Polymorphic ID
                        'sale_transaction_source_type' => MilkSale::class, // Polymorphic Type
                        'customer_id' => $milkSale->customer_id,
                        'transaction_date' => $validated['sale_date'],
                        'amount' => $paidAmount,
                        'payment_method' => 'Cash', // Default to Cash, can be made dynamic if needed
                        'reference_number' => MilkSale::generateInvoiceNumber(),
                        'notes' => 'Payment received during sale creation.',
                        'farm_id' => $milkSale->farm_id,
                        'user_id' => $milkSale->user_id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }

        return redirect()->route('milk-sales.index')->with('success', 'Milk sale updated successfully.');
    }

    public function destroy(MilkSale $milkSale)
    {
        $this->authorize('delete', $milkSale);

        DB::transaction(function () use ($milkSale) {
            // Delete any associated sale transactions
            $milkSale->saleTransactions()->delete();

            // Delete the milk sale itself
            $milkSale->delete();
        });

        return redirect()->route('milk-sales.index')->with('success', 'Milk sale deleted successfully.');
    }
}
