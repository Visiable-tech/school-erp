<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\CalendarEventType;
use App\Models\SchoolCalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolCalendarController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $eventTypes = CalendarEventType::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = SchoolCalendarEvent::with([
                'academicYear',
                'eventType'
            ])
            ->where('school_id', $schoolId);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('calendar_event_type_id')) {
            $query->where(
                'calendar_event_type_id',
                $request->calendar_event_type_id
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'start_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'start_at',
                '<=',
                $request->to_date
            );
        }

        $events = $query
            ->orderByDesc('start_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'school-calendar.index',
            compact(
                'events',
                'academicYears',
                'eventTypes'
            )
        );
    }


    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $eventTypes = CalendarEventType::where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'school-calendar.create',
            compact(
                'academicYears',
                'eventTypes'
            )
        );
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validateEvent(
            $request,
            $schoolId
        );

        SchoolCalendarEvent::create([
            'school_id' => $schoolId,

            'academic_year_id' =>
                $validated['academic_year_id'],

            'calendar_event_type_id' =>
                $validated['calendar_event_type_id'] ?? null,

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'start_at' =>
                $validated['start_at'],

            'end_at' =>
                $validated['end_at'] ?? null,

            'is_all_day' =>
                $request->boolean('is_all_day'),

            'is_holiday' =>
                $request->boolean('is_holiday'),

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('school-calendar.index')
            ->with(
                'success',
                'Calendar event created successfully.'
            );
    }


    public function edit(
        SchoolCalendarEvent $schoolCalendarEvent
    ) {
        $this->checkSchoolAccess(
            $schoolCalendarEvent
        );

        $schoolId =
            $schoolCalendarEvent->school_id;

        $academicYears = AcademicYear::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $eventTypes = CalendarEventType::where(
                'school_id',
                $schoolId
            )
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view(
            'school-calendar.edit',
            compact(
                'schoolCalendarEvent',
                'academicYears',
                'eventTypes'
            )
        );
    }


    public function update(
        Request $request,
        SchoolCalendarEvent $schoolCalendarEvent
    ) {
        $this->checkSchoolAccess(
            $schoolCalendarEvent
        );

        $validated = $this->validateEvent(
            $request,
            $schoolCalendarEvent->school_id
        );

        $schoolCalendarEvent->update([

            'academic_year_id' =>
                $validated['academic_year_id'],

            'calendar_event_type_id' =>
                $validated['calendar_event_type_id'] ?? null,

            'title' =>
                $validated['title'],

            'description' =>
                $validated['description'] ?? null,

            'start_at' =>
                $validated['start_at'],

            'end_at' =>
                $validated['end_at'] ?? null,

            'is_all_day' =>
                $request->boolean('is_all_day'),

            'is_holiday' =>
                $request->boolean('is_holiday'),

            'status' =>
                $request->boolean('status'),
        ]);

        return redirect()
            ->route('school-calendar.index')
            ->with(
                'success',
                'Calendar event updated successfully.'
            );
    }


    public function destroy(
        SchoolCalendarEvent $schoolCalendarEvent
    ) {
        $this->checkSchoolAccess(
            $schoolCalendarEvent
        );

        $schoolCalendarEvent->delete();

        return redirect()
            ->route('school-calendar.index')
            ->with(
                'success',
                'Calendar event deleted successfully.'
            );
    }


    private function validateEvent(
        Request $request,
        int $schoolId
    ): array {

        return $request->validate([

            'academic_year_id' => [
                'required',

                Rule::exists('academic_years', 'id')
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                    ),
            ],

            'calendar_event_type_id' => [
                'nullable',

                Rule::exists(
                    'calendar_event_types',
                    'id'
                )->where(
                    fn ($query) =>
                    $query->where(
                        'school_id',
                        $schoolId
                    )
                ),
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'start_at' => [
                'required',
                'date',
            ],

            'end_at' => [
                'nullable',
                'date',
                'after_or_equal:start_at',
            ],

        ]);
    }


    private function checkSchoolAccess(
        SchoolCalendarEvent $event
    ): void {

        if (
            $event->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}