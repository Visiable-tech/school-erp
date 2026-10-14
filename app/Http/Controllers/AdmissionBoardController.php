<?php

namespace App\Http\Controllers;

use App\Models\AdmissionBoard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdmissionBoardController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $query = AdmissionBoard::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                (int) $request->status
            );
        }

        $boards = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admission.boards.index',
            compact('boards')
        );
    }

    public function create()
    {
        return view('admission.boards.create');
    }

    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('admission_boards', 'name')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in(['board', 'university']),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        AdmissionBoard::create([
            'school_id' => $schoolId,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admission-boards.index')
            ->with(
                'success',
                'Board/University created successfully.'
            );
    }

    public function edit(AdmissionBoard $admissionBoard)
    {
        $this->checkSchool($admissionBoard);

        return view(
            'admission.boards.edit',
            compact('admissionBoard')
        );
    }

    public function update(
        Request $request,
        AdmissionBoard $admissionBoard
    ) {
        $this->checkSchool($admissionBoard);

        $schoolId = Auth::user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique('admission_boards', 'name')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    )
                    ->ignore($admissionBoard->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'type' => [
                'required',
                Rule::in(['board', 'university']),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $admissionBoard->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admission-boards.index')
            ->with(
                'success',
                'Board/University updated successfully.'
            );
    }

    public function destroy(AdmissionBoard $admissionBoard)
    {
        $this->checkSchool($admissionBoard);

        $admissionBoard->delete();

        return redirect()
            ->route('admission-boards.index')
            ->with(
                'success',
                'Board/University deleted successfully.'
            );
    }

    private function checkSchool(AdmissionBoard $admissionBoard): void
    {
        abort_if(
            $admissionBoard->school_id != Auth::user()->school_id,
            403
        );
    }
}