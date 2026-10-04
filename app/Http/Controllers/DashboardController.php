<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentViolation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();
        $attendances = StudentAttendance::query()->whereDate('attendance_date', $today);

        return view('dashboard', [
            'statistics' => [
                'students' => Student::query()->count(),
                'present' => (clone $attendances)->where('status', StudentAttendance::STATUS_PRESENT)->count(),
                'late' => (clone $attendances)->where('is_late', true)->count(),
                'violations' => StudentViolation::query()->whereDate('violation_date', $today)->count(),
            ],
        ]);
    }
}
