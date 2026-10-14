<?php

namespace App\Http\Controllers;

use App\Models\AdmissionInformationSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdmissionInformationSourceController extends Controller
{
    private function schoolId(): int
    {
        $schoolId = Auth::user()?->school_id;

        abort_if(!$schoolId, 403, 'School is not assigned.');

        return (int) $schoolId;
    }

    private function checkSchool(AdmissionInformationSource $source): void
    {
        abort_unless($source->school_id === $this->schoolId(), 403);
    }

    private function validatedData(Request $request, ?AdmissionInformationSource $source = null): array
    {
        $unique = Rule::unique('admission_information_sources', 'name')
            ->where('school_id', $this->schoolId());

        if ($source) {
            $unique->ignore($source->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:150', $unique],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ]);
    }

    public function index(Request $request)
    {
        $query = AdmissionInformationSource::query()
            ->where('school_id', $this->schoolId());

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (in_array($request->input('status'), ['0', '1'], true)) {
            $query->where('status', (int) $request->input('status'));
        }

        $sources = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admission.information-sources.index', compact('sources'));
    }

    public function create()
    {
        return view('admission.information-sources.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $data['school_id'] = $this->schoolId();
        $data['created_by'] = Auth::id();
        $data['sort_order'] = $data['sort_order'] ?? 0;

        AdmissionInformationSource::create($data);

        return redirect()
            ->route('admission-information-sources.index')
            ->with('success', 'Source of Information created successfully.');
    }

    public function edit(AdmissionInformationSource $admissionInformationSource)
    {
        $this->checkSchool($admissionInformationSource);

        return view('admission.information-sources.edit', [
            'source' => $admissionInformationSource,
        ]);
    }

    public function update(
        Request $request,
        AdmissionInformationSource $admissionInformationSource
    ) {
        $this->checkSchool($admissionInformationSource);

        $data = $this->validatedData($request, $admissionInformationSource);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $admissionInformationSource->update($data);

        return redirect()
            ->route('admission-information-sources.index')
            ->with('success', 'Source of Information updated successfully.');
    }

    public function destroy(AdmissionInformationSource $admissionInformationSource)
    {
        $this->checkSchool($admissionInformationSource);

        $admissionInformationSource->delete();

        return redirect()
            ->route('admission-information-sources.index')
            ->with('success', 'Source of Information deleted successfully.');
    }
}