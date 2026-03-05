<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Enums\AttendanceStatus;
use App\Enums\AttendanceSource;
use App\Models\EmployeeShift;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Attendance::class, 'attendance');
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $q = $request->input('q');

        $attendances = Attendance::with(['employee', 'farm', 'user'])
            ->when($user->hasRole('farm owner'), function ($query) use ($user) {
                $query->where('farm_id', $user->farm_id);
            })
            ->when($q, fn($qb) => $qb->whereHas('employee', function ($query) use ($q) {
                $query->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%");
            })->orWhere('date', 'like', "%$q%"))
            ->orderBy('date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Attendances/Index', [
            'attendances' => $attendances,
            'filters' => $request->only('q'),
        ]);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $users = User::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $attendanceStatuses = array_column(AttendanceStatus::cases(), 'value');
        $attendanceSources = array_column(AttendanceSource::cases(), 'value');

        return Inertia::render('Attendances/Create', [
            'employees' => $employees,
            'farms' => $farms,
            'users' => $users,
            'attendanceStatuses' => $attendanceStatuses,
            'attendanceSources' => $attendanceSources,
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'farm_id' => 'required|exists:farms,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'source' => 'required|in:' . implode(',', array_column(AttendanceSource::cases(), 'value')),
        ]);

        $validated['user_id'] = $user->id;

        $attendanceData = $this->calculateAttendanceStatus($validated['employee_id'], $validated['date'], $validated['check_in'], $validated['check_out']);

        Attendance::create([
            'employee_id' => $validated['employee_id'],
            'farm_id' => $validated['farm_id'],
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'check_in' => Carbon::parse($validated['check_in'])->format('Y-m-d H:i:s'),
            'check_out' => Carbon::parse($validated['check_out'])->format('Y-m-d H:i:s'),
            'status' => $attendanceData['status'],
            'source' => $validated['source'],
            'working_minutes' => $attendanceData['working_minutes'],
            'overtime_minutes' => $attendanceData['overtime_minutes'],
        ]);

        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully!');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['employee', 'farm', 'user']);
        return Inertia::render('Attendances/Show', ['attendance' => $attendance]);
    }

    public function edit(Attendance $attendance)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $employees = Employee::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();
        $farms = Farm::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('id', $user->farm_id);
        })->get();
        $users = User::when($user->hasRole('farm owner'), function ($query) use ($user) {
            $query->where('farm_id', $user->farm_id);
        })->get();

        $attendanceStatuses = array_column(AttendanceStatus::cases(), 'value');
        $attendanceSources = array_column(AttendanceSource::cases(), 'value');

        return Inertia::render('Attendances/Edit', [
            'attendance' => $attendance,
            'employees' => $employees,
            'farms' => $farms,
            'users' => $users,
            'attendanceStatuses' => $attendanceStatuses,
            'attendanceSources' => $attendanceSources,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'farm_id' => 'required|exists:farms,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'source' => 'required|in:' . implode(',', array_column(AttendanceSource::cases(), 'value')),
        ]);

        $attendanceData = $this->calculateAttendanceStatus($validated['employee_id'], $validated['date'], $validated['check_in'], $validated['check_out']);

        $attendance->update([
            'employee_id' => $validated['employee_id'],
            'farm_id' => $validated['farm_id'],
            'date' => $validated['date'],
            'check_in' => Carbon::parse($validated['check_in'])->format('Y-m-d H:i:s'),
            'check_out' => Carbon::parse($validated['check_out'])->format('Y-m-d H:i:s'),
            'status' => $attendanceData['status'],
            'source' => $validated['source'],
            'working_minutes' => $attendanceData['working_minutes'],
            'overtime_minutes' => $attendanceData['overtime_minutes'],
        ]);

        return redirect()->route('attendances.show', $attendance)->with('success', 'Attendance updated successfully!');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully!');
    }

    /**
     * Calculate attendance status, working minutes, and overtime minutes.
     *
     * @param int $employeeId
     * @param string $date
     * @param string|null $checkIn
     * @param string|null $checkOut
     * @return array
     */
    private function calculateAttendanceStatus(
        int $employeeId,
        string $date,
        ?string $checkIn,
        ?string $checkOut
    ): array {
        $status = AttendanceStatus::Absent->value;
        $workingMinutes = 0;
        $overtimeMinutes = 0;

        if (!$checkIn) {
            return [
                'status' => $status,
                'working_minutes' => $workingMinutes,
                'overtime_minutes' => $overtimeMinutes,
            ];
        }

        $employeeShift = EmployeeShift::with('shift')
            ->where('employee_id', $employeeId)
            ->whereDate('effective_from', '<=', $date)
            ->whereDate('effective_to', '>=', $date)
            ->first();

        // Build full datetime for the given date + time inputs.
        // The UI sends "HH:mm" (no date), so parsing without the date can lead to wrong diffs.
        $checkInTime = Carbon::parse($date . ' ' . $checkIn);

        if (!$employeeShift || !$employeeShift->shift) {
            // If no shift is assigned, fall back to raw diff between check-in/out (if available)
            // so Working Minutes doesn't stay 0 for biometric/imported records.
            $status = AttendanceStatus::Present->value;

            if ($checkOut) {
                $checkOutTime = Carbon::parse($date . ' ' . $checkOut);

                // Handle overnight (check-out after midnight)
                if ($checkOutTime->lessThan($checkInTime)) {
                    $checkOutTime->addDay();
                }

                $workingMinutes = $checkInTime->diffInMinutes($checkOutTime);
            }

            return [
                'status' => $status,
                'working_minutes' => $workingMinutes,
                'overtime_minutes' => $overtimeMinutes,
            ];
        }

        $shift = $employeeShift->shift;
        $shiftStart = Carbon::parse($date . ' ' . $shift->start_time);
        $shiftEnd   = Carbon::parse($date . ' ' . $shift->end_time);

        // Handle overnight shifts (e.g., 22:00 -> 06:00)
        if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
            $shiftEnd->addDay();
        }
        $grace      = $shift->grace_minutes ?? 0;

        /** STATUS */
        if ($checkInTime->greaterThan($shiftStart->copy()->addMinutes($grace))) {
            $status = AttendanceStatus::Late->value;
        } else {
            $status = AttendanceStatus::Present->value;
        }

        if (!$checkOut) {
            return [
                'status' => $status,
                'working_minutes' => $workingMinutes,
                'overtime_minutes' => $overtimeMinutes,
            ];
        }

        $checkOutTime = Carbon::parse($date . ' ' . $checkOut);

        // Handle overnight (check-out after midnight)
        if ($checkOutTime->lessThan($checkInTime)) {
            $checkOutTime->addDay();
        }

        /** WORKING MINUTES (bounded by shift) */
        $actualStart = $checkInTime->greaterThan($shiftStart)
            ? $checkInTime
            : $shiftStart;

        $actualEnd = $checkOutTime->lessThan($shiftEnd)
            ? $checkOutTime
            : $shiftEnd;

        if ($actualEnd->greaterThan($actualStart)) {
            $workingMinutes = $actualStart->diffInMinutes($actualEnd);
        }

        /** OVERTIME (only after shift end) */
        if ($checkOutTime->greaterThan($shiftEnd)) {
            $overtimeMinutes = $shiftEnd->diffInMinutes($checkOutTime);
        }

        return [
            'status' => $status,
            'working_minutes' => $workingMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ];
    }
}
