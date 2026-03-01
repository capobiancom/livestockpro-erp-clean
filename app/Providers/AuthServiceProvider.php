<?php

namespace App\Providers;

use App\Models\Calf;
use App\Models\CalvingRecord;
use App\Models\PregnancyCheckup;
use App\Models\EventType;
use App\Models\Medicine;
use App\Policies\CalfPolicy;
use App\Policies\CalvingRecordPolicy;
use App\Policies\EventTypePolicy;
use App\Policies\MedicinePolicy;
use App\Policies\PregnancyCheckupPolicy;
use App\Models\VaccinationRecord; // Import VaccinationRecord model
use App\Policies\VaccinationRecordPolicy; // Import VaccinationRecordPolicy
use App\Models\Category; // Import Category model
use App\Models\StockMovement; // Import StockMovement model
use App\Policies\InventoryCategoryPolicy; // Import InventoryCategoryPolicy
use App\Policies\StockMovementPolicy; // Import StockMovementPolicy
use App\Models\Disease; // Import Disease model
use App\Policies\DiseasePolicy; // Import DiseasePolicy
use App\Models\Department; // Import Department model
use App\Policies\DepartmentPolicy; // Import DepartmentPolicy
use App\Models\Designation; // Import Designation model
use App\Policies\DesignationPolicy; // Import DesignationPolicy
use App\Models\EmployeeDocument; // Import EmployeeDocument model
use App\Policies\EmployeeDocumentPolicy; // Import EmployeeDocumentPolicy
use App\Models\Attendance; // Import Attendance model
use App\Policies\AttendancePolicy; // Import AttendancePolicy
use App\Models\LeaveType; // Import LeaveType model
use App\Policies\LeaveTypePolicy; // Import LeaveTypePolicy
use App\Models\PayrollRun; // Import PayrollRun model
use App\Models\JournalEntry;
use App\Models\SaleTransaction;
use App\Policies\JournalEntryPolicy;
use App\Policies\PayrollRunPolicy; // Import PayrollRunPolicy
use App\Policies\SaleTransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PregnancyCheckup::class => PregnancyCheckupPolicy::class,
        CalvingRecord::class => CalvingRecordPolicy::class,
        Calf::class => CalfPolicy::class,
        EventType::class => EventTypePolicy::class,
        Medicine::class => MedicinePolicy::class,
        VaccinationRecord::class => VaccinationRecordPolicy::class,
        Category::class => InventoryCategoryPolicy::class, // Register InventoryCategory policy
        StockMovement::class => StockMovementPolicy::class, // Register StockMovement policy
        Disease::class => DiseasePolicy::class, // Register Disease policy
        Department::class => DepartmentPolicy::class, // Register Department policy
        Designation::class => DesignationPolicy::class, // Register Designation policy
        EmployeeDocument::class => EmployeeDocumentPolicy::class, // Register EmployeeDocument policy
        Attendance::class => AttendancePolicy::class, // Register Attendance policy
        LeaveType::class => LeaveTypePolicy::class, // Register LeaveType policy
        PayrollRun::class => PayrollRunPolicy::class, // Register PayrollRun policy
        \App\Models\PayrollItem::class => \App\Policies\PayrollItemPolicy::class,
        SaleTransaction::class => SaleTransactionPolicy::class,
        JournalEntry::class => JournalEntryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
