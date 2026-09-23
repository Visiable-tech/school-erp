<?php

namespace App\Http\Controllers;

use App\Models\CalendarEventType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarEventTypeController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $eventTypes = CalendarEventType::where(
                'school_id',
                $schoolId
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view(
            'calendar-event-types.index',
            compact('eventTypes')
        );
    }


    public function create()
    {
        return view('calendar-event-types.create');
    }


    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'calendar_event_types',
                    'name'
                )->where(
                    fn ($query) =>
                    $query->where(
                        'school_id',
                        $schoolId
                    )
                ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        CalendarEventType::create([

            'school_id' => $schoolId,

            'name' => $validated['name'],

            'code' => $validated['code'] ?? null,

            'is_holiday' =>
                $request->boolean('is_holiday'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route('calendar-event-types.index')
            ->with(
                'success',
                'Calendar event type created successfully.'
            );
    }


    public function edit(
        CalendarEventType $calendarEventType
    ) {
        $this->checkSchoolAccess($calendarEventType);

        return view(
            'calendar-event-types.edit',
            compact('calendarEventType')
        );
    }


    public function update(
        Request $request,
        CalendarEventType $calendarEventType
    ) {
        $this->checkSchoolAccess($calendarEventType);

        $schoolId = $calendarEventType->school_id;


        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'calendar_event_types',
                    'name'
                )
                    ->where(
                        fn ($query) =>
                        $query->where(
                            'school_id',
                            $schoolId
                        )
                    )
                    ->ignore($calendarEventType->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

        ]);


        $calendarEventType->update([

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'is_holiday' =>
                $request->boolean('is_holiday'),

            'sort_order' =>
                $validated['sort_order'] ?? 0,

            'status' =>
                $request->boolean('status'),

        ]);


        return redirect()
            ->route('calendar-event-types.index')
            ->with(
                'success',
                'Calendar event type updated successfully.'
            );
    }


    public function destroy(
        CalendarEventType $calendarEventType
    ) {
        $this->checkSchoolAccess($calendarEventType);


        if (
            $calendarEventType
                ->calendarEvents()
                ->exists()
        ) {

            return back()->with(
                'error',
                'This event type is already used in the school calendar and cannot be deleted.'
            );
        }


        $calendarEventType->delete();


        return redirect()
            ->route('calendar-event-types.index')
            ->with(
                'success',
                'Calendar event type deleted successfully.'
            );
    }


    private function checkSchoolAccess(
        CalendarEventType $calendarEventType
    ): void {

        if (
            $calendarEventType->school_id
            != auth()->user()->school_id
        ) {
            abort(403);
        }
    }
}