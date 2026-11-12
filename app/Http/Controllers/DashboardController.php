<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    private function has(string $table, array $columns = []): bool
    {
        if (!Schema::hasTable($table)) return false;
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) return false;
        }
        return true;
    }

    private function firstColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $column) {
            if (Schema::hasColumn($table, $column)) return $column;
        }
        return null;
    }

    private function scoped(string $table, int $schoolId)
    {
        return DB::table($table)->where('school_id', $schoolId);
    }

    public function index()
    {
        $schoolId = (int) auth()->user()->school_id;
        $today = Carbon::today(config('app.timezone', 'Asia/Kolkata'));
        $startMonth = $today->copy()->startOfMonth()->toDateString();
        $endMonth = $today->copy()->endOfMonth()->toDateString();
        $totalStudents = Student::where('school_id', $schoolId)->count();

        $year = null;
        if ($this->has('academic_years', ['school_id', 'name'])) {
            $query = $this->scoped('academic_years', $schoolId);
            if (Schema::hasColumn('academic_years', 'status')) $query->where('status', 1);
            $year = $query->orderByDesc('id')->first();
        }
        $yearId = $year->id ?? null;
        $yearName = $year->name ?? 'No academic year configured';

        $enrolledStudents = null;
        if ($this->has('student_enrollments', ['school_id', 'student_id'])) {
            $q = $this->scoped('student_enrollments', $schoolId);
            if ($yearId && Schema::hasColumn('student_enrollments', 'academic_year_id')) $q->where('academic_year_id', $yearId);
            if (Schema::hasColumn('student_enrollments', 'is_current')) $q->where('is_current', 1);
            $enrolledStudents = $q->distinct()->count('student_id');
        }

        $totalClasses = $this->has('school_classes', ['school_id']) ? $this->scoped('school_classes', $schoolId)->count() : null;
        $totalSections = $this->has('sections', ['school_id']) ? $this->scoped('sections', $schoolId)->count() : null;
        $totalUsers = $this->has('users', ['school_id']) ? $this->scoped('users', $schoolId)->count() : null;

        $classDistribution = collect();
        if ($this->has('student_enrollments', ['school_id', 'student_id', 'school_class_id']) && $this->has('school_classes', ['id', 'name'])) {
            $q = DB::table('student_enrollments')->where('student_enrollments.school_id', $schoolId)
                ->join('school_classes', 'school_classes.id', '=', 'student_enrollments.school_class_id')
                ->select('school_classes.name', DB::raw('COUNT(DISTINCT student_enrollments.student_id) AS total'));
            if ($yearId && Schema::hasColumn('student_enrollments', 'academic_year_id')) $q->where('student_enrollments.academic_year_id', $yearId);
            if (Schema::hasColumn('student_enrollments', 'is_current')) $q->where('student_enrollments.is_current', 1);
            $classDistribution = $q->groupBy('school_classes.id', 'school_classes.name')->orderBy('school_classes.name')->get();
        }

        $attendance = null;
        foreach (['student_attendances', 'student_attendance', 'attendances'] as $table) {
            if (!$this->has($table, ['school_id', 'student_id'])) continue;
            $dateCol = $this->firstColumn($table, ['attendance_date', 'date']);
            $statusCol = $this->firstColumn($table, ['attendance_status', 'status']);
            if (!$dateCol || !$statusCol) continue;
            $q = $this->scoped($table, $schoolId)->whereDate($dateCol, $today->toDateString());
            $records = $q->select('student_id', $statusCol)->get()->unique('student_id');
            $attendance = ['present' => 0, 'absent' => 0, 'late' => 0, 'recorded' => $records->count()];
            foreach ($records as $record) {
                $status = strtolower(trim((string) $record->{$statusCol}));
                if (in_array($status, ['present', 'p', '1'], true)) $attendance['present']++;
                elseif (in_array($status, ['absent', 'a', '0'], true)) $attendance['absent']++;
                elseif (in_array($status, ['late', 'l'], true)) $attendance['late']++;
            }
            break;
        }

        // Finance tables/columns vary across ERP installations. Never invent amounts.
        $collections = null;
        $collectionSource = null;
        foreach (['fee_collections', 'fee_payments', 'fee_transactions'] as $table) {
            if (!$this->has($table, ['school_id'])) continue;
            $amount = $this->firstColumn($table, ['amount_paid', 'paid_amount', 'amount', 'total_amount']);
            $date = $this->firstColumn($table, ['payment_date', 'collection_date', 'paid_at', 'created_at']);
            if (!$amount || !$date) continue;
            $collectionSource = compact('table', 'amount', 'date');
            $collections = (float) $this->scoped($table, $schoolId)->whereBetween(DB::raw("DATE(`$date`)"), [$startMonth, $endMonth])->sum($amount);
            break;
        }

        $expenses = null;
        $expenseSource = null;
        foreach (['expenses', 'school_expenses'] as $table) {
            if (!$this->has($table, ['school_id'])) continue;
            $amount = $this->firstColumn($table, ['amount', 'total_amount']);
            $date = $this->firstColumn($table, ['expense_date', 'date', 'created_at']);
            if (!$amount || !$date) continue;
            $expenseSource = compact('table', 'amount', 'date');
            $expenses = (float) $this->scoped($table, $schoolId)->whereBetween(DB::raw("DATE(`$date`)"), [$startMonth, $endMonth])->sum($amount);
            break;
        }

        $months = [];
        $monthlyCollections = [];
        $monthlyExpenses = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $today->copy()->startOfMonth()->subMonths($i);
            $months[] = $month->format('M y');
            $monthStart = $month->toDateString();
            $monthEnd = $month->copy()->endOfMonth()->toDateString();
            $monthlyCollections[] = $collectionSource
                ? (float) $this->scoped($collectionSource['table'], $schoolId)
                    ->whereBetween(DB::raw('DATE(`' . $collectionSource['date'] . '`)'), [$monthStart, $monthEnd])
                    ->sum($collectionSource['amount'])
                : null;
            $monthlyExpenses[] = $expenseSource
                ? (float) $this->scoped($expenseSource['table'], $schoolId)
                    ->whereBetween(DB::raw('DATE(`' . $expenseSource['date'] . '`)'), [$monthStart, $monthEnd])
                    ->sum($expenseSource['amount'])
                : null;
        }

        $events = collect();
        if ($this->has('school_calendar_events', ['school_id'])) {
            $dateCol = $this->firstColumn('school_calendar_events', ['event_date', 'start_date', 'start_at', 'date']);
            $titleCol = $this->firstColumn('school_calendar_events', ['title', 'event_name', 'name']);
            if ($dateCol && $titleCol) {
                $events = $this->scoped('school_calendar_events', $schoolId)
                    ->whereDate($dateCol, '>=', $today->toDateString())
                    ->orderBy($dateCol)->limit(8)->get()
                    ->map(fn ($event) => [
                        'title' => $event->{$titleCol},
                        'date' => Carbon::parse($event->{$dateCol})->format('d M Y'),
                    ]);
            }
        }

        return view('dashboard', compact(
            'totalStudents', 'yearName', 'enrolledStudents', 'totalClasses', 'totalSections',
            'totalUsers', 'classDistribution', 'attendance', 'collections', 'expenses',
            'months', 'monthlyCollections', 'monthlyExpenses', 'events'
        ));
    }
}
