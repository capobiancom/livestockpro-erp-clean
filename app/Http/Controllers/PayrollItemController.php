<?php

namespace App\Http\Controllers;

use App\Data\PayrollItemData;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\LeaveRequest;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\SalaryStructure;
use App\Models\Setting;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayrollItemController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PayrollItem::class, 'payroll_item');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');
        $payrollRunId = $request->input('payroll_run_id');

        $payrollItems = PayrollItem::with(['payrollRun', 'employee'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->whereHas('payrollRun', function ($q) use ($user) {
                    $q->where('farm_id', $user->farm_id);
                });
            })
            ->when($payrollRunId, function ($qb) use ($payrollRunId) {
                $qb->where('payroll_run_id', $payrollRunId);
            })
            ->when($q, function ($qb) use ($q) {
                $qb->whereHas('employee', function ($qEmployee) use ($q) {
                    $qEmployee->where('first_name', 'like', "%$q%")
                        ->orWhere('last_name', 'like', "%$q%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('PayrollItems/Index', [
            'payrollItems' => $payrollItems,
            'filters' => $request->only('q', 'payroll_run_id'),
            'payrollRuns' => PayrollRun::when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })->get(['id', 'month', 'year']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $payrollRunId = $request->input('payroll_run_id');
        $payrollRun = null;
        if ($payrollRunId) {
            $payrollRun = PayrollRun::findOrFail($payrollRunId);
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $payrollRuns = PayrollRun::query()
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->get(['id', 'month', 'year']);

        return Inertia::render('PayrollItems/Create', [
            'payrollRun' => $payrollRun, // This will be used to pre-select a payroll run if payroll_run_id is provided
            'payrollRuns' => $payrollRuns, // This will be used to populate the dropdown
            'employees' => $employees,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PayrollItemData $data)
    {
        PayrollItem::create($data->toArray());
        return redirect()->route('payroll-items.index')->with('success', 'Payroll Item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PayrollItem $payrollItem)
    {
        $payrollItem->load(['payrollRun', 'employee']);
        return Inertia::render('PayrollItems/Show', ['payrollItem' => $payrollItem]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PayrollItem $payrollItem)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        return Inertia::render('PayrollItems/Edit', [
            'payrollItem' => $payrollItem,
            'employees' => $employees,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PayrollItemData $data, PayrollItem $payrollItem)
    {
        $payrollItem->update($data->toArray());
        return redirect()->route('payroll-items.show', $payrollItem)->with('success', 'Payroll Item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PayrollItem $payrollItem)
    {
        $payrollItem->delete();
        return redirect()->route('payroll-items.index')->with('success', 'Payroll Item deleted successfully!');
    }

    /**
     * Display the payslip for the specified resource.
     */
    public function showPayslip(PayrollItem $payrollItem)
    {
        $payrollItem->load(['payrollRun', 'employee.department', 'employee.designation']);
        return Inertia::render('Payslips/Show', ['payrollItem' => $payrollItem]);
    }

    /**
     * Display the print-friendly payslip for the specified resource.
     */
    public function printPayslip(PayrollItem $payrollItem)
    {
        $payrollItem->load(['payrollRun', 'employee.department', 'employee.designation']);
        return Inertia::render('Payslips/Print', ['payrollItem' => $payrollItem]);
    }

    public function generateSalarySheet(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $payrollRunId = $request->input('payroll_run_id');

        if (!$payrollRunId) {
            return back()->with('error', 'Please select a Payroll Run to generate the salary sheet.');
        }

        $payrollRun = PayrollRun::findOrFail($payrollRunId);

        $payrollItems = PayrollItem::with(['employee.department', 'employee.designation'])
            ->where('payroll_run_id', $payrollRunId)
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->whereHas('payrollRun', function ($q) use ($user) {
                    $q->where('farm_id', $user->farm_id);
                });
            })
            ->get();

        $appSettings = Setting::where('farm_id', $user->farm_id)->first();

        return Inertia::render('PayrollItems/SalarySheet', [
            'payrollRun' => $payrollRun,
            'payrollItems' => $payrollItems,
            'appSettings' => $appSettings,
        ]);
    }

    /**
     * Generate payroll items for all employees for a given payroll run.
     */
    public function generatePayroll(Request $request, PayrollRun $payrollRun)
    {
        $this->authorize('update', $payrollRun);

        if (in_array($payrollRun->status, ['finalized', 'paid'])) {
            return back()->with('error', 'Payroll already finalized.');
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        DB::transaction(function () use ($payrollRun, $user) {

            // Remove existing payroll items (we will decide what to do with the journal after recalculation)
            $payrollRun->payrollItems()->delete();

            $startDate = Carbon::create($payrollRun->year, $payrollRun->month, 1)->startOfMonth();
            $endDate   = Carbon::create($payrollRun->year, $payrollRun->month, 1)->endOfMonth();

            $employees = Employee::where('farm_id', $payrollRun->farm_id)
                ->where('status', 'active')
                ->get();

            $totalNetSalary = 0.0;

            foreach ($employees as $employee) {

                /**
                 * 1️⃣ Salary Structure
                 */
                $salaryStructure = SalaryStructure::where('employee_id', $employee->id)
                    ->where('effective_from', '<=', $startDate)
                    ->where(function ($q) use ($startDate) {
                        $q->whereNull('effective_to')
                            ->orWhere('effective_to', '>=', $startDate);
                    })
                    ->orderByDesc('effective_from')
                    ->first();

                if (!$salaryStructure) {
                    continue;
                }

                /**
                 * 2️⃣ Attendance Calculation
                 */
                $attendances = Attendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $overtimeMinutes = 0;
                $workingDays = 0;

                foreach ($attendances as $attendance) {

                    if (!$attendance->check_in || !$attendance->check_out) {
                        continue;
                    }

                    $attendanceDate = Carbon::parse($attendance->date)->toDateString();

                    $employeeShift = EmployeeShift::where('employee_id', $employee->id)
                        ->where('effective_from', '<=', $attendanceDate)
                        ->where(function ($q) use ($attendanceDate) {
                            $q->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $attendanceDate);
                        })
                        ->with('shift')
                        ->orderByDesc('effective_from')
                        ->first();

                    if (!$employeeShift || !$employeeShift->shift) {
                        continue;
                    }

                    $checkIn  = Carbon::parse($attendance->check_in);
                    $checkOut = Carbon::parse($attendance->check_out);

                    $workedMinutes = $checkIn->diffInMinutes($checkOut);

                    $shiftStart = Carbon::parse($attendanceDate . ' ' . $employeeShift->shift->start_time);
                    $shiftEnd   = Carbon::parse($attendanceDate . ' ' . $employeeShift->shift->end_time);

                    if ($shiftEnd->lt($shiftStart)) {
                        $shiftEnd->addDay();
                    }

                    $scheduledMinutes = $shiftStart->diffInMinutes($shiftEnd);

                    if ($workedMinutes > $scheduledMinutes) {
                        $overtimeMinutes += ($workedMinutes - $scheduledMinutes);
                    }

                    $workingDays++;
                }

                /**
                 * 3️⃣ Leave Calculation
                 */

                $approvedLeaves = LeaveRequest::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate]);
                    })
                    ->with('leaveType')
                    ->get();

                $paidLeaveDays = 0;
                $unpaidLeaveDays = 0;

                foreach ($approvedLeaves as $leave) {

                    $leaveStart = Carbon::parse($leave->start_date)->max($startDate);
                    $leaveEnd   = Carbon::parse($leave->end_date)->min($endDate);

                    $days = $leaveStart->diffInDays($leaveEnd) + 1;

                    if ($leave->leaveType->paid) {
                        $paidLeaveDays += $days;
                    } else {
                        $unpaidLeaveDays += $days;
                    }
                }


                /**
                 * 4️⃣ Salary Calculation
                 */
                $basicSalary      = $salaryStructure->basic_salary;
                $houseAllowance   = $salaryStructure->house_allowance ?? 0;
                $medicalAllowance = $salaryStructure->medical_allowance ?? 0;
                $transportAllowance = $salaryStructure->transport_allowance ?? 0;

                $bonus = $employee->bonus ?? 0;
                $festivalBonus = $employee->festival_bonus ?? 0;
                $performanceIncentive = $employee->performance_incentive ?? 0;

                $taxAmount = $employee->tax_amount ?? 0;
                $loanDeduction = $employee->loan_deduction ?? 0;
                $otherDeductions = $employee->other_deductions ?? 0;

                $overtimeHours  = round($overtimeMinutes / 60, 2);
                $overtimeRate   = $salaryStructure->overtime_rate ?? 0;
                $overtimeAmount = round($overtimeHours * $overtimeRate, 2);

                $grossSalary = $basicSalary
                    + $houseAllowance
                    + $medicalAllowance
                    + $transportAllowance
                    + $overtimeAmount;
                +$bonus
                    + $festivalBonus
                    + $performanceIncentive;


                // Monthly deduction logic
                $totalDaysInMonth = $startDate->daysInMonth;
                $perDaySalary = $basicSalary / $totalDaysInMonth;

                $leaveDeduction = round($perDaySalary * $unpaidLeaveDays, 2);

                $totalDeductions = ($leaveDeduction + $taxAmount + $loanDeduction + $otherDeductions);

                $netSalary = round($grossSalary - $totalDeductions, 2);

                $totalNetSalary += (float) $netSalary;

                /**
                 * 5️⃣ Save Snapshot
                 */
                PayrollItem::create([
                    'payroll_run_id'     => $payrollRun->id,
                    'employee_id'        => $employee->id,
                    'working_days'       => $workingDays,
                    'paid_leave_days'    => $paidLeaveDays,
                    'unpaid_leave_days'  => $unpaidLeaveDays,
                    'leave_deduction'    => $leaveDeduction,
                    'bonus'              => $bonus,
                    'festival_bonus'     => $festivalBonus,
                    'performance_incentive' => $performanceIncentive,
                    'tax_amount'         => $taxAmount,
                    'loan_deduction'     => $loanDeduction,
                    'other_deductions'   => $otherDeductions,
                    'basic_salary'       => $basicSalary,
                    'house_allowance'    => $houseAllowance,
                    'medical_allowance'  => $medicalAllowance,
                    'transport_allowance' => $transportAllowance,
                    'overtime_hours'     => $overtimeHours,
                    'overtime_rate'      => $overtimeRate,
                    'overtime_amount'    => $overtimeAmount,
                    'gross_salary'       => round($grossSalary, 2),
                    'deductions'         => $totalDeductions,
                    'net_salary'         => $netSalary,
                ]);
            }

            // Accounts integration: Salary Expense (DR) vs Salaries Payable (CR)
            $salaryExpenseAccount = ChartOfAccount::query()
                ->where('code', '5005')
                ->first();

            if (!$salaryExpenseAccount) {
                throw new \Exception('Chart of account 5005 (Salary Expense) not found.');
            }

            $salariesPayableAccount = ChartOfAccount::query()
                ->where('code', '2002')
                ->first();

            if (!$salariesPayableAccount) {
                throw new \Exception('Chart of account 2002 (Salaries Payable) not found.');
            }

            // If payroll already generated and journal already posted:
            // - If recalculated salary amount equals existing posted payroll journal amount for the month, do nothing.
            // - If different, delete previous payroll journal for the month and post a new one.
            $existingEntry = JournalEntry::query()
                ->where('farm_id', $payrollRun->farm_id)
                ->where('reference_type', 'payroll_run')
                ->where('reference_id', $payrollRun->id)
                ->where('status', 'posted')
                ->with('lines')
                ->first();

            $existingAmount = 0.0;
            if ($existingEntry) {
                // Salary Expense line is debit; use sum of debits as the journal amount
                $existingAmount = (float) $existingEntry->lines->sum('debit_amount');
            }

            $amountChanged = $existingEntry && (round($existingAmount, 2) !== round((float) $totalNetSalary, 2));

            if ($existingEntry && !$amountChanged) {
                // Amount is same: keep existing journal, no re-posting
                return;
            }

            if ($existingEntry && $amountChanged) {
                // Amount differs: delete previous journal and re-post
                $existingEntry->delete();
            }

            if ($totalNetSalary > 0) {
                $entry = JournalEntry::query()->create([
                    'farm_id' => $payrollRun->farm_id,
                    'user_id' => $user?->id,
                    'entry_date' => $startDate->toDateString(),
                    'reference_type' => 'payroll_run',
                    'reference_id' => $payrollRun->id,
                    'description' => 'Payroll generated for ' . $payrollRun->month . '/' . $payrollRun->year,
                    'status' => 'posted',
                    'created_by' => $user?->id,
                ]);

                $entry->lines()->createMany([
                    [
                        'account_id' => $salaryExpenseAccount->id,
                        'debit_amount' => $totalNetSalary,
                        'credit_amount' => 0,
                        'narration' => 'Salary Expense',
                    ],
                    [
                        'account_id' => $salariesPayableAccount->id,
                        'debit_amount' => 0,
                        'credit_amount' => $totalNetSalary,
                        'narration' => 'Salaries Payable',
                    ],
                ]);
            }
        });

        return redirect()
            ->route('payroll-runs.show', $payrollRun)
            ->with('success', 'Payroll items generated successfully.');
    }
}
