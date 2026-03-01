<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\ArtificialInsemination;
use App\Models\Attendance;
use App\Models\Breed;
use App\Models\Calf;
use App\Models\CalvingRecord;
use App\Models\CashAccount;
use App\Models\Category;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Disease;
use App\Models\DiseaseTreatment;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeShift;
use App\Models\EventType;
use App\Models\Expense;
use App\Models\Farm;
use App\Models\FeedingItem;
use App\Models\FeedingRecord;
use App\Models\FeedType;
use App\Models\FixedAsset;
use App\Models\HealthEvent;
use App\Models\HealthIssue;
use App\Models\Herd;
use App\Models\InventoryItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Logistic;
use App\Models\Medicine;
use App\Models\MedicineGroup;
use App\Models\MilkRecord;
use App\Models\MilkSale;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\Pregnancy;
use App\Models\PregnancyCheckup;
use App\Models\Purchase;
use App\Models\ReproductionRecord;
use App\Models\Sale;
use App\Models\SalaryStructure;
use App\Models\SalesItem;
use App\Models\Shift;
use App\Models\StaffProfile;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Treatment;
use App\Models\User;
use App\Models\VaccinationRecord;
use App\Models\VaccineType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DemoSeedingController extends Controller
{
    public function seed(Request $request, Farm $farm): RedirectResponse
    {
        $farmId = (int) ($request->input('farm') ?: $farm->id);

        try {
            $farm = Farm::query()->findOrFail($farmId);

            Log::info('DemoSeedingController: Starting seeding', [
                'farm_id' => $farmId,
                'user_id' => $request->user()?->id,
            ]);

            if ($farm->demo_data_seeded) {
                return redirect()
                    ->route('dashboard', ['farm_id' => $farm->id, 'show_demo_seeding_popup' => false])
                    ->with('info', 'Demo data already seeded for this farm.');
            }

            $lock = Cache::lock("demo-seed:farm:{$farmId}", 120);

            $lock->block(10, function () use ($farmId, $request) {
                DB::transaction(function () use ($farmId, $request) {
                    // NOTE: "Actual demo data" — deterministic, human-readable records that are
                    // internally consistent and linked to the given farm (no Faker).
                    $userId = $request->user()?->id ?? \App\Models\User::query()->value('id');

                    // ─── Core livestock ────────────────────────────────────────────────────

                    $herds = collect([
                        ['name' => 'Dairy Herd', 'description' => 'Milking cows'],
                        ['name' => 'Heifers', 'description' => 'Young females'],
                        ['name' => 'Calves', 'description' => 'Newborn and young calves'],
                    ])->map(function (array $data) use ($farmId) {
                        return Herd::query()->firstOrCreate(
                            ['farm_id' => $farmId, 'name' => $data['name']],
                            ['description' => $data['description']]
                        );
                    });

                    $breeds = collect([
                        'Holstein Friesian',
                        'Jersey',
                        'Sahiwal',
                        'Red Chittagong',
                        'Crossbred',
                    ])->map(function (string $name) use ($farmId) {
                        return Breed::query()->firstOrCreate(
                            ['farm_id' => $farmId, 'name' => $name],
                            []
                        );
                    });

                    $supplier = Supplier::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => 'AgroVet Supplies'],
                        [
                            'phone' => '01700000000',
                            'address' => 'Local market',
                        ]
                    );

                    $animalsSeed = [
                        ['tag' => 'COW-0001', 'name' => 'Maya',  'sex' => 'female', 'dob' => now()->subYears(5)->toDateString(),  'breed' => 'Holstein Friesian', 'herd' => 'Dairy Herd'],
                        ['tag' => 'COW-0002', 'name' => 'Rani',  'sex' => 'female', 'dob' => now()->subYears(4)->toDateString(),  'breed' => 'Jersey',            'herd' => 'Dairy Herd'],
                        ['tag' => 'COW-0003', 'name' => 'Lily',  'sex' => 'female', 'dob' => now()->subYears(3)->toDateString(),  'breed' => 'Crossbred',         'herd' => 'Heifers'],
                        ['tag' => 'BULL-0001', 'name' => 'Kalo',  'sex' => 'male',   'dob' => now()->subYears(4)->toDateString(),  'breed' => 'Sahiwal',           'herd' => 'Heifers'],
                        ['tag' => 'CALF-0001', 'name' => 'Choto', 'sex' => 'female', 'dob' => now()->subMonths(8)->toDateString(), 'breed' => 'Crossbred',         'herd' => 'Calves'],
                        ['tag' => 'CALF-0002', 'name' => 'Tuku',  'sex' => 'male',   'dob' => now()->subMonths(6)->toDateString(), 'breed' => 'Red Chittagong',    'herd' => 'Calves'],
                    ];

                    $animals = collect($animalsSeed)->map(function (array $a) use ($farmId, $userId, $breeds, $herds, $supplier) {
                        $breedId = $breeds->firstWhere('name', $a['breed'])?->id;
                        $herdId  = $herds->firstWhere('name', $a['herd'])?->id;

                        $tag = $a['tag'];
                        if (Animal::query()->where('farm_id', $farmId)->where('tag', $tag)->exists()) {
                            $tag = $tag . '-' . Str::upper(Str::random(4));
                        }

                        return Animal::query()->create([
                            'farm_id'            => $farmId,
                            'user_id'            => $userId,
                            'supplier_id'        => $supplier->id,
                            'herd_id'            => $herdId,
                            'breed_id'           => $breedId,
                            'tag'                => $tag,
                            'name'               => $a['name'],
                            'sex'                => $a['sex'],
                            'dob'                => $a['dob'],
                            'status'             => 'active',
                            'current_weight_kg'  => 350,
                            'color'              => 'Black/White',
                            'acquired_at'        => now()->subYears(2)->toDateString(),
                            'attributes'         => null,
                            'notes'              => 'Demo animal',
                        ]);
                    });

                    // Feed types (used in feeding records context)
                    collect([
                        'Concentrate Mix',
                        'Green Fodder',
                        'Straw',
                        'Mineral Supplement',
                    ])->each(fn($name) => FeedType::query()->firstOrCreate(['farm_id' => $farmId, 'name' => $name], []));

                    // Feeding records: last 7 days × 3 animals — collected for FeedingItems below
                    $feedingRecords = collect();
                    foreach (range(1, 7) as $daysAgo) {
                        foreach ($animals->take(3) as $animal) {
                            $feedingRecords->push(FeedingRecord::query()->create([
                                'farm_id'      => $farmId,
                                'animal_id'    => $animal->id,
                                'feeding_date' => now()->subDays($daysAgo)->toDateString(),
                                'feeding_time' => 'morning',
                                'notes'        => 'Morning feeding',
                                'user_id'      => $userId,
                            ]));
                        }
                    }

                    // ─── Health ────────────────────────────────────────────────────────────

                    $vaccineTypes = collect([
                        'FMD Vaccine',
                        'HS Vaccine',
                        'BQ Vaccine',
                    ])->map(fn($n) => VaccineType::query()->firstOrCreate(['farm_id' => $farmId, 'name' => $n], []));

                    foreach ($animals->take(4) as $animal) {
                        VaccinationRecord::query()->create([
                            'farm_id'          => $farmId,
                            'animal_id'        => $animal->id,
                            'vaccine_type_id'  => $vaccineTypes->first()->id ?? null,
                            'vaccination_date' => now()->subDays(30)->toDateString(),
                            'next_due_date'    => now()->addDays(150)->toDateString(),
                            'notes'            => 'Routine vaccination',
                        ]);
                    }

                    $healthIssues = collect([
                        ['name' => 'Mastitis', 'severity' => 'moderate'],
                        ['name' => 'Fever',    'severity' => 'mild'],
                        ['name' => 'Lameness', 'severity' => 'moderate'],
                    ])->map(function (array $d) use ($farmId, $userId, $animals) {
                        return HealthIssue::query()->create([
                            'farm_id'      => $farmId,
                            'user_id'      => $userId,
                            'animal_id'    => $animals->random()->id,
                            'name'         => $d['name'],
                            'severity'     => $d['severity'],
                            'diagnosed_at' => now()->subDays(7)->toDateString(),
                            'status'       => 'active',
                            'notes'        => 'Demo health issue',
                        ]);
                    });

                    $healthEventType = EventType::query()->firstOrCreate(['farm_id' => $farmId, 'name' => 'Health Check'], []);

                    $healthEvents = collect();
                    foreach ($animals->take(3) as $animal) {
                        $healthEvents->push(HealthEvent::query()->create([
                            'farm_id'         => $farmId,
                            'animal_id'       => $animal->id,
                            'health_issue_id' => $healthIssues->first()->id ?? null,
                            'event_type_id'   => $healthEventType->id,
                            'event_date'      => now()->subDays(7)->toDateString(),
                            'notes'           => 'Observed symptoms and recorded event',
                        ]));
                    }

                    collect(['FMD', 'Anthrax', 'Brucellosis', 'Worm infestation', 'Tick fever'])
                        ->each(fn($name) => Disease::query()->firstOrCreate(
                            ['farm_id' => $farmId, 'name' => $name],
                            ['user_id' => $userId]
                        ));

                    $treatments = collect([
                        'Antibiotic course',
                        'Anti-inflammatory',
                        'Deworming',
                    ])->map(fn($n) => Treatment::query()->firstOrCreate(['farm_id' => $farmId, 'name' => $n], []));

                    $diseaseTreatments = collect();
                    foreach ($healthEvents as $he) {
                        $diseaseTreatments->push(DiseaseTreatment::query()->create([
                            'farm_id'          => $farmId,
                            'user_id'          => $userId,
                            'health_issue_id'  => $he->health_issue_id,
                            'health_event_id'  => $he->id,
                            'treatment_id'     => $treatments->first()->id ?? null,
                            'notes'            => 'Treatment started',
                        ]));
                    }

                    // ─── Reproduction: Records, AI, Pregnancies, Calving, Calves ──────────

                    $femaleAnimals = $animals->where('sex', 'female')->values();

                    // Reproduction records — 3 service events for female animals
                    $reproductionRecords = $femaleAnimals->take(3)->map(
                        fn($animal) =>
                        ReproductionRecord::query()->create([
                            'farm_id'    => $farmId,
                            'user_id'    => $userId,
                            'animal_id'  => $animal->id,
                            'event'      => 'service',
                            'event_date' => now()->subDays(90)->toDateString(),
                            'outcome'    => 'pending',
                            'notes'      => 'Demo reproduction record',
                        ])
                    );

                    // Artificial Inseminations — for first 2 reproduction records
                    foreach ($reproductionRecords->take(2) as $idx => $repRecord) {
                        ArtificialInsemination::query()->create([
                            'farm_id'               => $farmId,
                            'user_id'               => $userId,
                            'reproduction_record_id' => $repRecord->id,
                            'semen_batch_no'        => 'AI-BATCH-' . str_pad((string)($idx + 1), 3, '0', STR_PAD_LEFT),
                            'breed_id'              => $breeds->firstWhere('name', 'Holstein Friesian')?->id,
                            'semen_company'         => 'National AI Center',
                            'insemination_date'     => now()->subDays(88)->toDateString(),
                            'cost'                  => 500,
                            'remarks'               => 'Routine AI procedure',
                        ]);
                    }

                    // Ongoing pregnancies — for first 2 female animals
                    // DB enum only allows: 'ongoing', 'aborted', 'completed'
                    $pregnancies = $femaleAnimals->take(2)->map(function ($animal, $idx) use ($farmId, $userId, $reproductionRecords) {
                        return Pregnancy::query()->create([
                            'farm_id'                   => $farmId,
                            'user_id'                   => $userId,
                            'animal_id'                 => $animal->id,
                            'reproduction_record_id'    => $reproductionRecords->get($idx)->id,
                            'pregnancy_confirmed_date'  => now()->subDays(75)->toDateString(),
                            'expected_calving_date'     => now()->addDays(208)->toDateString(),
                            'expected_gestation_days'   => 283,
                            'pregnancy_status'          => 'ongoing',
                            'health_notes'              => 'Good body condition, no complications.',
                        ]);
                    });

                    // Pregnancy checkups — 2 checkups per active pregnancy
                    foreach ($pregnancies as $pregnancy) {
                        foreach ([30, 15] as $daysAgo) {
                            PregnancyCheckup::query()->create([
                                'farm_id'        => $farmId,
                                'user_id'        => $userId,
                                'pregnancy_id'   => $pregnancy->id,
                                'checkup_date'   => now()->subDays($daysAgo)->toDateString(),
                                'checkup_result' => 'normal',
                                'observations'   => 'Foetus developing normally. No signs of distress.',
                                'checked_by'     => $userId,
                            ]);
                        }
                    }

                    // A completed pregnancy for calving demo — needs its own reproduction record
                    $oldReproRecord = ReproductionRecord::query()->create([
                        'farm_id'    => $farmId,
                        'user_id'    => $userId,
                        'animal_id'  => $femaleAnimals->first()->id,
                        'event'      => 'service',
                        'event_date' => now()->subDays(300)->toDateString(),
                        'outcome'    => 'success',
                        'notes'      => 'Historical service event (prior calving)',
                    ]);

                    $completedPregnancy = Pregnancy::query()->create([
                        'farm_id'                   => $farmId,
                        'user_id'                   => $userId,
                        'animal_id'                 => $femaleAnimals->first()->id,
                        'reproduction_record_id'    => $oldReproRecord->id,
                        'pregnancy_confirmed_date'  => now()->subDays(290)->toDateString(),
                        'expected_calving_date'     => now()->subDays(10)->toDateString(),
                        'expected_gestation_days'   => 283,
                        'pregnancy_status'          => 'completed',
                        'health_notes'              => 'Successfully completed gestation.',
                    ]);

                    // Calving record
                    CalvingRecord::query()->create([
                        'farm_id'         => $farmId,
                        'user_id'         => $userId,
                        'pregnancy_id'    => $completedPregnancy->id,
                        'calving_date'    => now()->subDays(10)->toDateString(),
                        'calving_type'    => 'normal',
                        'calves_count'    => 1,
                        'calf_gender'     => 'female',
                        'calving_outcome' => 'successful',
                        'notes'           => 'Normal delivery, healthy calf born.',
                    ]);

                    // Newborn calf
                    $calfTag = 'CLF-' . $farmId . '-0001';
                    if (!Calf::query()->where('farm_id', $farmId)->where('tag_number', $calfTag)->exists()) {
                        Calf::query()->create([
                            'farm_id'       => $farmId,
                            'user_id'       => $userId,
                            'mother_id'     => $femaleAnimals->first()->id,
                            'tag_number'    => $calfTag,
                            'gender'        => 'female',
                            'birth_date'    => now()->subDays(10)->toDateString(),
                            'birth_weight'  => 32.5,
                            'health_status' => 'healthy',
                        ]);
                    }

                    // ─── HR & Payroll ──────────────────────────────────────────────────────

                    $department = Department::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => 'Farm Operations'],
                        ['user_id' => $userId]
                    );
                    $designation = Designation::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => 'Farm Worker'],
                        ['user_id' => $userId, 'level' => 1]
                    );

                    $staffProfiles = collect([
                        ['first_name' => 'Rahim',  'last_name' => 'Uddin',  'phone' => '01800000001'],
                        ['first_name' => 'Karim',  'last_name' => 'Miah',   'phone' => '01800000002'],
                        ['first_name' => 'Ayesha', 'last_name' => 'Khatun', 'phone' => '01800000003'],
                    ])->map(fn(array $s) => StaffProfile::query()->create([
                        'farm_id'    => $farmId,
                        'user_id'    => $userId,
                        'first_name' => $s['first_name'],
                        'last_name'  => $s['last_name'],
                        'phone'      => $s['phone'],
                    ]));

                    $employees = $staffProfiles->map(function (StaffProfile $sp, int $idx) use ($farmId, $userId, $department, $designation) {
                        $baseCode    = 'EMP-' . str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT);
                        $employeeCode = $baseCode;

                        if (Employee::query()->where('farm_id', $farmId)->where('employee_code', $employeeCode)->exists()) {
                            $employeeCode = $baseCode . '-' . Str::upper(Str::random(4));
                        }

                        $userEmail = 'emp-' . $farmId . '-' . Str::lower(str_replace([' ', '/'], '-', $employeeCode)) . '@demo.test';

                        return Employee::query()->create([
                            'farm_id'          => $farmId,
                            'user_id'          => $userId,
                            'department_id'    => $department->id,
                            'designation_id'   => $designation->id,
                            'employee_code'    => $employeeCode,
                            'first_name'       => $sp->first_name,
                            'last_name'        => $sp->last_name,
                            'gender'           => $idx % 2 === 0 ? 'male' : 'female',
                            'date_of_birth'    => now()->subYears(25 + $idx)->toDateString(),
                            'phone'            => $sp->phone,
                            'email'            => null,
                            'address'          => 'Demo address',
                            'join_date'        => now()->subMonths(6)->toDateString(),
                            'employment_type'  => 'permanent',
                            'salary_type'      => 'monthly',
                            'status'           => 'active',
                            'user_email'       => $userEmail,
                            'password'         => 'password',
                        ]);
                    });

                    $shifts = collect([
                        ['name' => 'Morning Shift', 'start_time' => '06:00', 'end_time' => '14:00'],
                        ['name' => 'Evening Shift', 'start_time' => '14:00', 'end_time' => '22:00'],
                    ])->map(fn(array $d) => Shift::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'user_id' => $userId, 'name' => $d['name']],
                        ['start_time' => $d['start_time'], 'end_time' => $d['end_time']]
                    ));

                    foreach ($employees as $idx => $emp) {
                        EmployeeShift::query()->create([
                            'farm_id'       => $farmId,
                            'user_id'       => $userId,
                            'shift_id'      => $shifts[$idx % $shifts->count()]->id,
                            'employee_id'   => $emp->id,
                            'effective_from' => now()->toDateString(),
                        ]);
                    }

                    // Employee Documents
                    foreach ($employees->take(2) as $idx => $emp) {
                        EmployeeDocument::query()->create([
                            'farm_id'         => $farmId,
                            'user_id'         => $userId,
                            'employee_id'     => $emp->id,
                            'document_type'   => $idx === 0 ? 'National ID' : 'Contract',
                            'document_number' => 'DOC-' . $farmId . '-' . str_pad((string)($idx + 1), 4, '0', STR_PAD_LEFT),
                            'expiry_date'     => now()->addYears(2)->toDateString(),
                            // NOTE: `employee_documents.file_path` is NOT NULL in DB; use a deterministic demo placeholder.
                            'file_path'       => 'demo/employee-documents/' . $farmId . '/employee-' . $emp->id . '-' . ($idx === 0 ? 'nid' : 'contract') . '.pdf',
                        ]);
                    }

                    // Salary Structures
                    foreach ($employees as $emp) {
                        SalaryStructure::query()->create([
                            'farm_id'             => $farmId,
                            'user_id'             => $userId,
                            'employee_id'         => $emp->id,
                            'basic_salary'        => 15000,
                            'house_allowance'     => 2000,
                            'medical_allowance'   => 1000,
                            'transport_allowance' => 1500,
                            'overtime_rate'       => 80,
                            'effective_from'      => now()->subMonths(6)->toDateString(),
                        ]);
                    }

                    // Leave Types
                    $leaveTypes = collect([
                        ['name' => 'Annual Leave', 'paid' => true,  'max_days_per_year' => 14],
                        ['name' => 'Sick Leave',   'paid' => true,  'max_days_per_year' => 10],
                        ['name' => 'Casual Leave', 'paid' => false, 'max_days_per_year' => 7],
                    ])->map(fn($d) => LeaveType::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => $d['name']],
                        ['user_id' => $userId, 'paid' => $d['paid'], 'max_days_per_year' => $d['max_days_per_year']]
                    ));

                    // Leave Request — first employee requests 2 days casual leave
                    LeaveRequest::query()->create([
                        'farm_id'       => $farmId,
                        'user_id'       => $userId,
                        'employee_id'   => $employees->first()->id,
                        'leave_type_id' => $leaveTypes->firstWhere('name', 'Casual Leave')?->id,
                        'start_date'    => now()->addDays(3)->toDateString(),
                        'end_date'      => now()->addDays(4)->toDateString(),
                        'total_days'    => 2,
                        'reason'        => 'Personal work',
                        'status'        => 'pending',
                    ]);

                    // Attendance — last 7 days for each employee
                    foreach ($employees as $emp) {
                        foreach (range(1, 7) as $daysAgo) {
                            $date    = now()->subDays($daysAgo);
                            $isWeekend = (int) $date->format('N') >= 6; // Sat=6, Sun=7
                            $status  = $isWeekend ? 'absent' : 'present';
                            Attendance::query()->create([
                                'farm_id'          => $farmId,
                                'user_id'          => $userId,
                                'employee_id'      => $emp->id,
                                'date'             => $date->toDateString(),
                                'check_in'         => $status === 'present' ? '06:05:00' : null,
                                'check_out'        => $status === 'present' ? '14:10:00' : null,
                                'working_minutes'  => $status === 'present' ? 485 : 0,
                                'overtime_minutes' => 0,
                                'status'           => $status,
                                'source'           => 'manual',
                            ]);
                        }
                    }

                    // Payroll Run for last month
                    $payrollRun = PayrollRun::query()->create([
                        'farm_id'      => $farmId,
                        'user_id'      => $userId,
                        'month'        => now()->subMonth()->month,
                        'year'         => now()->subMonth()->year,
                        'status'       => 'approved',
                        'generated_at' => now()->subDays(5),
                    ]);

                    foreach ($employees as $emp) {
                        PayrollItem::query()->create([
                            'payroll_run_id'        => $payrollRun->id,
                            'employee_id'           => $emp->id,
                            'basic_salary'          => 15000,
                            'house_allowance'       => 2000,
                            'medical_allowance'     => 1000,
                            'transport_allowance'   => 1500,
                            'overtime_hours'        => 2,
                            'overtime_rate'         => 80,
                            'overtime_amount'       => 160,
                            'gross_salary'          => 19660,
                            'deductions'            => 500,
                            'net_salary'            => 19160,
                            'working_days'          => 26,
                            'paid_leave_days'       => 0,
                            'unpaid_leave_days'     => 0,
                            'leave_deduction'       => 0,
                            'bonus'                 => 0,
                            'festival_bonus'        => 0,
                            'performance_incentive' => 0,
                            'tax_amount'            => 500,
                            'loan_deduction'        => 0,
                            'other_deductions'      => 0,
                        ]);
                    }

                    // ─── Inventory ─────────────────────────────────────────────────────────

                    // Inventory Categories
                    $categories = collect([
                        ['name' => 'Animal Feed',        'description' => 'Feed and fodder for livestock'],
                        ['name' => 'Veterinary Supplies', 'description' => 'Medicines and medical supplies'],
                        ['name' => 'Farm Equipment',      'description' => 'Tools and equipment used on the farm'],
                    ])->map(fn($d) => Category::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => $d['name']],
                        ['user_id' => $userId, 'description' => $d['description']]
                    ));

                    // Medicine Groups
                    collect([
                        'Antibiotics',
                        'Antiparasitics',
                        'Vitamins & Supplements',
                    ])->each(fn($n) => MedicineGroup::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => $n],
                        ['user_id' => $userId]
                    ));

                    collect([
                        ['name' => 'Oxytetracycline',  'unit' => 'vial'],
                        ['name' => 'Ivermectin',        'unit' => 'vial'],
                        ['name' => 'Vitamin B Complex', 'unit' => 'bottle'],
                    ])->each(fn($m) => Medicine::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => $m['name']],
                        ['supplier_id' => $supplier->id, 'unit' => $m['unit']]
                    ));

                    $feedCategory = $categories->firstWhere('name', 'Animal Feed');
                    $vetCategory  = $categories->firstWhere('name', 'Veterinary Supplies');

                    $inventoryItems = collect([
                        ['name' => 'Cattle Feed Bag (50kg)', 'unit' => 'bag',  'unit_cost' => 2500, 'quantity' => 100, 'category_id' => $feedCategory?->id],
                        ['name' => 'Mineral Mix (1kg)',       'unit' => 'pack', 'unit_cost' => 300,  'quantity' => 100, 'category_id' => $feedCategory?->id],
                        ['name' => 'Dewormer Dose',           'unit' => 'dose', 'unit_cost' => 120,  'quantity' => 50,  'category_id' => $vetCategory?->id],
                    ])->map(fn(array $i) => InventoryItem::query()->create([
                        'farm_id'               => $farmId,
                        'user_id'               => $userId,
                        'supplier_id'           => $supplier->id,
                        'inventory_category_id' => $i['category_id'],
                        'name'                  => $i['name'],
                        'unit'                  => $i['unit'],
                        'unit_cost'             => $i['unit_cost'],
                        'quantity'              => $i['quantity'],
                        'min_quantity'          => 10,
                    ]));

                    // ─── Purchases & Supplier Payment ──────────────────────────────────────

                    $basePurchaseInvoice = 'PUR-' . $farmId . '-0001';
                    $purchaseInvoice = $basePurchaseInvoice;
                    if (Purchase::query()->where('farm_id', $farmId)->where('invoice_number', $purchaseInvoice)->exists()) {
                        $purchaseInvoice = $basePurchaseInvoice . '-' . Str::upper(Str::random(4));
                    }

                    $purchase = Purchase::query()->create([
                        'farm_id'        => $farmId,
                        'supplier_id'    => $supplier->id,
                        'invoice_number' => $purchaseInvoice,
                        'purchased_at'   => now()->subDays(20)->toDateString(),
                        'purchase_date'  => now()->subDays(20)->toDateString(),
                        'notes'          => 'Initial stock purchase (demo)',
                        'total_amount'   => 0,
                    ]);

                    $purchaseItems = [
                        [
                            'purchase_id' => $purchase->id,
                            'item_type'   => 'inventory_item',
                            'item_id'     => $inventoryItems->first()->id,
                            'quantity'    => 10,
                            'unit_price'  => 2500,
                            'sub_total'   => 10 * 2500,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ],
                        [
                            'purchase_id' => $purchase->id,
                            'item_type'   => 'inventory_item',
                            'item_id'     => $inventoryItems->get(1)->id,
                            'quantity'    => 20,
                            'unit_price'  => 300,
                            'sub_total'   => 20 * 300,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ],
                    ];
                    DB::table('purchase_items')->insert($purchaseItems);

                    $purchaseTotal = collect($purchaseItems)->sum('sub_total');
                    $purchase->forceFill(['total_amount' => $purchaseTotal])->save();

                    // Supplier Payment for the purchase
                    SupplierPayment::query()->create([
                        'farm_id'               => $farmId,
                        'user_id'               => $userId,
                        'supplier_id'           => $supplier->id,
                        'purchase_source_id'    => $purchase->id,
                        'purchase_source_type'  => Purchase::class,
                        'payment_date'          => now()->subDays(18)->toDateString(),
                        'amount'                => 20000,
                        'payment_method'        => 'Cash',
                        'reference_number'      => 'SPY-' . $farmId . '-0001',
                        'notes'                 => 'Partial payment for initial stock purchase',
                    ]);

                    // ─── Stock Movements ───────────────────────────────────────────────────

                    // Stock 'in' for each inventory item — FIFO-ready batch
                    foreach ($inventoryItems as $item) {
                        StockMovement::query()->create([
                            'farm_id'           => $farmId,
                            'user_id'           => $userId,
                            'item_type'         => InventoryItem::class,
                            'item_id'           => $item->id,
                            'movement_type'     => 'in',
                            'source_type'       => Purchase::class,
                            'source_event_type' => 'purchase',
                            'source_id'         => $purchase->id,
                            'quantity'          => $item->quantity,
                            'unit_cost'         => $item->unit_cost,
                            'batch_no'          => 'DEMO-BATCH-001',
                            'movement_date'     => now()->subDays(20)->toDateString(),
                        ]);
                    }

                    // Feeding Items — first 3 feeding records (one per animal, day 7 ago)
                    // Each record gets 2 bags Cattle Feed + 0.5 pack Mineral Mix
                    $feedItem1 = $inventoryItems->get(0); // Cattle Feed Bag
                    $feedItem2 = $inventoryItems->get(1); // Mineral Mix
                    foreach ($feedingRecords->take(3) as $fr) {
                        FeedingItem::query()->create([
                            'feeding_record_id' => $fr->id,
                            'item_id'           => $feedItem1->id,
                            'quantity'          => 2,
                        ]);
                        StockMovement::query()->create([
                            'farm_id'           => $farmId,
                            'user_id'           => $userId,
                            'item_type'         => InventoryItem::class,
                            'item_id'           => $feedItem1->id,
                            'movement_type'     => 'out',
                            'source_type'       => FeedingRecord::class,
                            'source_event_type' => 'consumption',
                            'source_id'         => $fr->id,
                            'quantity'          => 2,
                            'unit_cost'         => $feedItem1->unit_cost,
                            'batch_no'          => 'DEMO-BATCH-001',
                            'movement_date'     => $fr->feeding_date,
                        ]);

                        FeedingItem::query()->create([
                            'feeding_record_id' => $fr->id,
                            'item_id'           => $feedItem2->id,
                            'quantity'          => 0.5,
                        ]);
                        StockMovement::query()->create([
                            'farm_id'           => $farmId,
                            'user_id'           => $userId,
                            'item_type'         => InventoryItem::class,
                            'item_id'           => $feedItem2->id,
                            'movement_type'     => 'out',
                            'source_type'       => FeedingRecord::class,
                            'source_event_type' => 'consumption',
                            'source_id'         => $fr->id,
                            'quantity'          => 0.5,
                            'unit_cost'         => $feedItem2->unit_cost,
                            'batch_no'          => 'DEMO-BATCH-001',
                            'movement_date'     => $fr->feeding_date,
                        ]);
                    }

                    // ─── Logistics ─────────────────────────────────────────────────────────

                    Logistic::query()->create([
                        'farm_id' => $farmId,
                        'name'    => 'Pickup Van',
                        'notes'   => 'Used for feed and milk transport',
                    ]);

                    // ─── Accounts ──────────────────────────────────────────────────────────

                    $parent = collect([
                        ['code' => '1000', 'name' => 'Assets',                 'type' => 'asset'],
                        ['code' => '2000', 'name' => 'Liabilities',  'type' => 'liability'],
                        ['code' => '3000', 'name' => 'Owner Equity',         'type' => 'equity'],
                        ['code' => '4000', 'name' => 'Income',           'type' => 'income'],
                        ['code' => '5000', 'name' => 'Expenses',         'type' => 'expense']
                    ])->map(function (array $a) use ($farmId, $userId) {
                        $code = $a['code'];
                        if (ChartOfAccount::query()->where('farm_id', $farmId)->where('code', $code)->exists()) {
                            $code = $code . '-' . Str::upper(Str::random(4));
                        }
                        return ChartOfAccount::query()->firstOrCreate(
                            ['farm_id' => $farmId, 'user_id' => $userId, 'code' => $code],
                            ['name' => $a['name'], 'type' => $a['type']]
                        );
                    });

                    $child = collect([
                        ['code' => '1001', 'name' => 'Cash',  'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1002', 'name' => 'Bank', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1003', 'name' => 'Accounts Receivable', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1004', 'name' => 'Livestock Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id,  'type' => 'asset'],
                        ['code' => '1005', 'name' => 'Feed Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1006', 'name' => 'Fixed Assets', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1007', 'name' => 'InventoryItem Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1008', 'name' => 'Medicine Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1009', 'name' => 'Veterinary Supplies Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],
                        ['code' => '1010', 'name' => 'Farm Equipment Inventory', 'parent_id' => $parent->firstWhere('name', 'Assets')?->id, 'type' => 'asset'],

                        ['code' => '2001', 'name' => 'Accounts Payable', 'parent_id' => $parent->firstWhere('name', 'Liabilities')?->id, 'type' => 'liability'],
                        ['code' => '2002', 'name' => 'Salaries Payable', 'parent_id' => $parent->firstWhere('name', 'Liabilities')?->id, 'type' => 'liability'],
                        ['code' => '2003', 'name' => 'Tax Payable', 'parent_id' => $parent->firstWhere('name', 'Liabilities')?->id, 'type' => 'liability'],

                        ['code' => '3000', 'name' => 'Owner Equity', 'parent_id' => $parent->firstWhere('name', 'Owner Equity')?->id, 'type' => 'equity'],

                        ['code' => '4001', 'name' => 'Milk Sales',  'parent_id' => $parent->firstWhere('name', 'Income')?->id, 'type' => 'income'],
                        ['code' => '4002', 'name' => 'Animal Sales', 'parent_id' => $parent->firstWhere('name', 'Income')?->id, 'type' => 'income'],
                        ['code' => '4003', 'name' => 'Inventory SalesItem', 'parent_id' => $parent->firstWhere('name', 'Income')?->id, 'type' => 'income'],
                        ['code' => '4004', 'name' => 'Medicine SalesItem', 'parent_id' => $parent->firstWhere('name', 'Income')?->id, 'type' => 'income'],

                        ['code' => '5001', 'name' => 'Feed Expense',      'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5002', 'name' => 'Medicine Expense',  'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5003', 'name' => 'Veterinary Supplies Expense',  'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5004', 'name' => 'Lab Test Expense',  'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5005', 'name' => 'Salary Expense',  'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5006', 'name' => 'Labour Expense',    'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5007', 'name' => 'Inventory Sales Adj. Exp.',    'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5008', 'name' => 'Animal Sales Adj. Exp.',    'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5009', 'name' => 'Farm Equipment Expense',    'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5010', 'name' => 'Vet Fee', 'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5011', 'name' => 'Other Expense', 'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                        ['code' => '5012', 'name' => 'Artificial Insemination Expense', 'parent_id' => $parent->firstWhere('name', 'Expenses')?->id,   'type' => 'expense'],
                    ])->map(function (array $a) use ($farmId, $userId) {
                        $code = $a['code'];
                        $parentId = $a['parent_id'] ?? null;
                        if (ChartOfAccount::query()->where('farm_id', $farmId)->where('code', $code)->exists()) {
                            $code = $code . '-' . Str::upper(Str::random(4));
                        }
                        return ChartOfAccount::query()->firstOrCreate(
                            ['farm_id' => $farmId, 'user_id' => $userId, 'code' => $code, 'parent_id' => $parentId],
                            ['name' => $a['name'], 'type' => $a['type']]
                        );
                    });

                    // Cash & Bank Account
                    CashAccount::query()->create([
                        'farm_id'         => $farmId,
                        'user_id'         => $userId,
                        'account_id'      => $child->firstWhere('name', 'Cash')?->id,
                        'name'            => 'Main Cash Account',
                        'type'            => 'cash',
                        'opening_balance' => 50000,
                        'current_balance' => 68500,
                        'is_active'       => true,
                        'description'     => 'Primary cash account for farm operations',
                    ]);

                    // Fixed Assets Register
                    collect([
                        ['name' => 'Milking Machine',   'asset_type' => 'machinery',       'purchase_value' => 120000, 'useful_life_years' => 10],
                        ['name' => 'Feed Storage Silo', 'asset_type' => 'building',        'purchase_value' => 80000,  'useful_life_years' => 20],
                        ['name' => 'Delivery Van',      'asset_type' => 'vehicle',         'purchase_value' => 850000, 'useful_life_years' => 8],
                    ])->each(fn(array $a) => FixedAsset::query()->create([
                        'farm_id'             => $farmId,
                        'user_id'             => $userId,
                        'name'                => $a['name'],
                        'asset_type'          => $a['asset_type'],
                        'purchase_value'      => $a['purchase_value'],
                        'purchase_date'       => now()->subYear()->toDateString(),
                        'useful_life_years'   => $a['useful_life_years'],
                        'depreciation_method' => 'straight_line',
                        'status'              => 'active',
                        'location'            => 'Main Farm',
                    ]));

                    $saleEntry = JournalEntry::query()->create([
                        'farm_id'        => $farmId,
                        'user_id'        => $userId,
                        'entry_date'     => now()->subDays(10)->toDateString(),
                        'reference_type' => 'sale',
                        'reference_id'   => null,
                        'description'    => 'Demo sale entry',
                        'status'         => 'posted',
                        'created_by'     => $userId,
                    ]);

                    JournalEntryLine::query()->insert([
                        [
                            'journal_entry_id' => $saleEntry->id,
                            'account_id'       => $child->firstWhere('name', 'Cash')?->id,
                            'debit_amount'     => 5000,
                            'credit_amount'    => 0,
                            'narration'        => 'Cash received',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ],
                        [
                            'journal_entry_id' => $saleEntry->id,
                            'account_id'       => $child->firstWhere('name', 'Milk Sales')?->id,
                            'debit_amount'     => 0,
                            'credit_amount'    => 5000,
                            'narration'        => 'Milk sales income',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ],
                    ]);

                    $expenseEntry = JournalEntry::query()->create([
                        'farm_id'        => $farmId,
                        'user_id'        => $userId,
                        'entry_date'     => now()->subDays(5)->toDateString(),
                        'reference_type' => 'expense',
                        'reference_id'   => null,
                        'description'    => 'Demo expense entry',
                        'status'         => 'posted',
                        'created_by'     => $userId,
                    ]);

                    JournalEntryLine::query()->insert([
                        [
                            'journal_entry_id' => $expenseEntry->id,
                            'account_id'       => $child->firstWhere('name', 'Feed Expense')?->id,
                            'debit_amount'     => 1200,
                            'credit_amount'    => 0,
                            'narration'        => 'Feed expense',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ],
                        [
                            'journal_entry_id' => $expenseEntry->id,
                            'account_id'       => $child->firstWhere('name', 'Cash')?->id,
                            'debit_amount'     => 0,
                            'credit_amount'    => 1200,
                            'narration'        => 'Cash paid',
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ],
                    ]);

                    // ─── Customers & Sales ─────────────────────────────────────────────────

                    $customer = Customer::query()->firstOrCreate(
                        ['farm_id' => $farmId, 'name' => 'Walk-in Customer'],
                        [
                            'type'    => 'wholesaler',
                            'phone'   => '01900000000',
                            'address' => 'Local area',
                        ]
                    );

                    $sale = Sale::query()->create([
                        'farm_id'        => $farmId,
                        'user_id'        => $userId,
                        'customer_id'    => $customer->id,
                        'invoice_number' => Sale::generateInvoiceNumber(),
                        'invoice_date'   => now()->subDays(3)->toDateString(),
                        'total_amount'   => 0,
                        'paid_amount'    => 0,
                        'status'         => 'unpaid',
                    ]);

                    SalesItem::query()->insert([[
                        'sale_id'     => $sale->id,
                        'item_id'     => $inventoryItems->first()->id,
                        'item_type'   => InventoryItem::class,
                        'quantity'    => 2,
                        'unit_price'  => 2500,
                        'total_price' => 5000,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]]);

                    $sale->forceFill(['total_amount' => 5000, 'paid_amount' => 2000, 'status' => 'partial'])->save();

                    // ─── Production ────────────────────────────────────────────────────────

                    foreach ($animals->where('sex', 'female')->take(2) as $animal) {
                        MilkRecord::query()->create([
                            'farm_id'              => $farmId,
                            'animal_id'            => $animal->id,
                            'date'                 => now()->subDays(1)->toDateString(),
                            'record_date'          => now()->subDays(1)->toDateString(),
                            'morning_milk_liters'  => 6,
                            'evening_milk_liters'  => 5,
                            'notes'                => 'Daily milk record',
                        ]);
                    }

                    $baseMilkSaleRef = 'MILK-SALE-' . $farmId . '-0001';
                    $milkSaleRef = $baseMilkSaleRef;
                    if (MilkSale::query()->where('reference', $milkSaleRef)->exists()) {
                        $milkSaleRef = $baseMilkSaleRef . '-' . Str::upper(Str::random(4));
                    }
                    MilkSale::query()->create([
                        'farm_id'    => $farmId,
                        'reference'  => $milkSaleRef,
                        'customer_id' => $supplier->id,
                        'sale_date'  => now()->subDays(2)->toDateString(),
                        'quantity'   => 50,
                        'unit'       => 'liters',
                        'unit_price' => 60,
                        'total_price' => 3000,
                        'notes'      => 'Milk sold to local buyer',
                    ]);

                    // ─── Finance ───────────────────────────────────────────────────────────

                    Expense::query()->create([
                        'farm_id'     => $farmId,
                        'user_id'     => $userId,
                        'title'       => 'Feed purchase',
                        'incurred_on' => now()->subDays(4)->toDateString(),
                        'amount'      => 1200,
                        'notes'       => 'Feed purchase (demo)',
                    ]);

                    EventType::query()->firstOrCreate(['farm_id' => $farmId, 'name' => 'Vet Visit'], []);
                });
            });

            $farm->forceFill(['demo_data_seeded' => true])->save();

            return redirect()
                ->route('dashboard', ['farm_id' => $farm->id, 'show_demo_seeding_popup' => false])
                ->with('success', 'Demo data seeded successfully!');
        } catch (\Throwable $e) {
            Log::error('Demo data seeding failed', [
                'farm_id'   => $farmId ?? null,
                'exception' => $e,
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to seed demo data. Please check logs for details.');
        }
    }

    public function skip(Request $request, Farm $farm_model): RedirectResponse
    {
        $farmId = (int) ($request->input('farm') ?: $farm_model->id);

        $farm = Farm::query()->findOrFail($farmId);
        $farm->forceFill(['demo_data_seeded' => true])->save();

        return redirect()
            ->route('dashboard', ['farm_id' => $farm->id, 'show_demo_seeding_popup' => false])
            ->with('info', 'Demo data seeding skipped.');
    }
}
