<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleDashboardController extends Controller
{
    private function countRows(string $table, ?int $schoolId): ?int
    {
        if (!Schema::hasTable($table)) return null;
        $query = DB::table($table);
        if (Schema::hasColumn($table, 'school_id')) {
            if (!$schoolId) return null;
            $query->where('school_id', $schoolId);
        } else {
            // Avoid counting global/unscoped records for a school-specific dashboard.
            return null;
        }
        return $query->count();
    }

    private function dashboard(Request $request, string $module, array $cards, array $links)
    {
        $user = $request->user();
        abort_unless($user, 403);
        $schoolId = $user->school_id ? (int) $user->school_id : null;
        abort_unless($schoolId, 403, 'Assign a school to your user account first.');

        $stats = [];
        foreach ($cards as $title => $table) {
            $stats[] = ['title' => $title, 'value' => $this->countRows($table, $schoolId)];
        }
        return view('module-dashboards.index', compact('module', 'stats', 'links'));
    }

    public function admission(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['Admin', 'Super Admin']) ||
            $request->user()->can('admission-application.view') ||
            $request->user()->can('admission-candidate.view'), 403);

        return $this->dashboard($request, 'Admission', [
            'Candidates' => 'admission_candidates',
            'Enquiries' => 'admission_enquiries',
            'Registrations' => 'admission_applications',
            'Form Sales' => 'admission_form_sales',
        ], [
            ['label'=>'Candidates','route'=>'admission-candidates.index'],
            ['label'=>'Enquiries','route'=>'admission-enquiries.index'],
            ['label'=>'Registrations','route'=>'admission-applications.index'],
            ['label'=>'Form Sales','route'=>'admission-form-sales.index'],
        ]);
    }

    public function students(Request $request)
    {
        abort_unless($request->user()->hasAnyRole(['Admin', 'Super Admin']) ||
            $request->user()->can('student.view'), 403);

        return $this->dashboard($request, 'Student Information', [
            'Students' => 'students',
            'Enrollments' => 'student_enrollments',
            'Student Documents' => 'student_documents',
            'Promotion Records' => 'student_promotions',
        ], [
            ['label'=>'Student List','route'=>'students.index'],
            ['label'=>'Student Documents','route'=>'student-documents.index'],
            ['label'=>'Promotions','route'=>'student-promotions.index'],
        ]);
    }
}
