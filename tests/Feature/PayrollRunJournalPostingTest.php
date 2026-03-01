<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Farm;
use App\Models\JournalEntry;
use App\Models\PayrollRun;
use App\Models\SalaryStructure;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollRunJournalPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_payroll_generate_posts_salary_expense_and_salaries_payable_and_regenerating_adjusts(): void
    {
        $farm = Farm::query()->create([
            'name' => 'Test Farm',
        ]);

        $user = User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'farm_id' => $farm->id,
        ]);

        // Create only the accounts we need for this test (avoid ChartOfAccountSeeder dependencies)
        $salaryExpense = ChartOfAccount::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'code' => '5005',
            'name' => 'Salary Expense',
            'type' => \App\Enums\ChartOfAccountType::Expense,
            'is_system' => true,
            'is_active' => true,
        ]);

        $salariesPayable = ChartOfAccount::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'code' => '2002',
            'name' => 'Salaries Payable',
            'type' => \App\Enums\ChartOfAccountType::Liability,
            'is_system' => true,
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'name' => 'Test Department',
        ]);

        $designation = Designation::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'name' => 'Test Designation',
            'level' => 1,
        ]);

        $employee = Employee::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'department_id' => $department->id,
            'designation_id' => $designation->id,
            'employee_code' => 'EMP-0001',
            'first_name' => 'Test',
            'last_name' => 'Employee',
            'gender' => 'male',
            'join_date' => now()->subMonth()->toDateString(),
            'employment_type' => \App\Enums\EmploymentType::Permanent->value,
            'salary_type' => \App\Enums\SalaryType::Monthly->value,
            'user_email' => 'employee@example.com',
            'status' => 'active',
        ]);

        SalaryStructure::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'employee_id' => $employee->id,
            'effective_from' => now()->startOfMonth()->toDateString(),
            'basic_salary' => 1000,
            'house_allowance' => 0,
            'medical_allowance' => 0,
            'transport_allowance' => 0,
            'overtime_rate' => 0,
        ]);

        $payrollRun = PayrollRun::query()->create([
            'farm_id' => $farm->id,
            'user_id' => $user->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'status' => 'draft',
        ]);

        // Seed permissions so policy checks don't throw PermissionDoesNotExist
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // Give the user the permission required by PayrollRunPolicy@update
        $user->givePermissionTo('payroll-runs.update');

        $this->actingAs($user);

        // 1) Generate payroll
        $res = $this->post(route('payroll-runs.generate-payroll', $payrollRun));
        $res->assertRedirect();

        $entry = JournalEntry::query()
            ->where('reference_type', 'payroll_run')
            ->where('reference_id', $payrollRun->id)
            ->first();

        $this->assertNotNull($entry);

        $this->assertEquals('posted', $entry->status);

        $debit = (float) $entry->lines()->where('account_id', $salaryExpense->id)->value('debit_amount');
        $credit = (float) $entry->lines()->where('account_id', $salariesPayable->id)->value('credit_amount');

        $this->assertEquals(1000.0, $debit);
        $this->assertEquals(1000.0, $credit);

        // 2) Regenerate without changing salary -> should NOT create a new journal (amount same)
        $resSame = $this->post(route('payroll-runs.generate-payroll', $payrollRun));
        $resSame->assertRedirect();

        $entriesSame = JournalEntry::query()
            ->where('reference_type', 'payroll_run')
            ->where('reference_id', $payrollRun->id)
            ->get();

        $this->assertCount(1, $entriesSame, 'Expected exactly one payroll_run journal entry after regeneration with same amount.');

        $entrySame = $entriesSame->first();
        $debitSame = (float) $entrySame->lines()->where('account_id', $salaryExpense->id)->value('debit_amount');
        $creditSame = (float) $entrySame->lines()->where('account_id', $salariesPayable->id)->value('credit_amount');

        $this->assertEquals(1000.0, $debitSame);
        $this->assertEquals(1000.0, $creditSame);

        // 3) Change salary and regenerate -> should delete old entry and post new amount
        $employee->update(['tax_amount' => 100]); // net salary becomes 900

        $res2 = $this->post(route('payroll-runs.generate-payroll', $payrollRun));
        $res2->assertRedirect();

        $entries = JournalEntry::query()
            ->where('reference_type', 'payroll_run')
            ->where('reference_id', $payrollRun->id)
            ->get();

        $this->assertCount(1, $entries, 'Expected exactly one payroll_run journal entry after regeneration with changed amount.');

        $entry2 = $entries->first();

        $debit2 = (float) $entry2->lines()->where('account_id', $salaryExpense->id)->value('debit_amount');
        $credit2 = (float) $entry2->lines()->where('account_id', $salariesPayable->id)->value('credit_amount');

        $this->assertEquals(900.0, $debit2);
        $this->assertEquals(900.0, $credit2);
    }
}
