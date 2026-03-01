<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Enums\CustomerType;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $customers = Customer::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->input('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->input('farm_id'), function ($query, $farmId) {
                $query->where('farm_id', $farmId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'type', 'farm_id']),
            'customerTypes' => CustomerType::cases(),
            'statistics' => [
                'total_customers' => Customer::where('farm_id', auth()->user()->farm_id)->count(),
                'individual_customers' => Customer::where('farm_id', auth()->user()->farm_id)->where('type', CustomerType::Individual->value)->count(),
                'business_customers' => Customer::where('farm_id', auth()->user()->farm_id)->where('type', CustomerType::Business->value)->count(),
                'customers_with_farms' => Customer::where('farm_id', auth()->user()->farm_id)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return Inertia::render('Customers/Create', [
            'farms' => Farm::all(['id', 'name']),
            'users' => User::all(['id', 'name']),
            'customerTypes' => CustomerType::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {

        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['farm_id'] = auth()->user()->farm_id;
        Customer::create($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {

        $customer->load(['farm', 'user']);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $customer->load(['farm', 'user']);

        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
            'customerTypes' => CustomerType::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = auth()->id();
        $validatedData['farm_id'] = auth()->user()->farm_id;
        $customer->update($validatedData);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
