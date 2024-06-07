<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ZoomMeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('dashboard.teacher.zoom_meeting');
    }

    public function create()
    {
        $user = Auth::user();
        $courses = [];

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = $user->teacher->courses;
        }

        return view('dashboard.teacher.create_zoom_meetings', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic' => 'required',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
            'invite_option' => 'required|in:all,teachers,course_specific',
            'course_id' => 'required_if:invite_option,course_specific|exists:courses,id',
            'url' => 'required|url',
        ]);

        ZoomMeeting::create([
            'user_id' => Auth::id(),
            'course_id' => $request->course_id,
            'topic' => $request->topic,
            'agenda' => $request->agenda,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'url' => $request->url,
        ]);

        return response()->json(['message' => 'Meeting URL created successfully.']);
    }

    public function show(ZoomMeeting $zoomMeeting)
    {
        return response()->json($zoomMeeting);
    }

    public function edit(ZoomMeeting $zoomMeeting)
    {
        return view('dashboard.teacher.create_zoom_meetings', ['zoomMeeting' => $zoomMeeting, 'courses' => Course::all()]);
    }

    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        $request->validate([
            'topic' => 'required',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
            'invite_option' => 'required|in:all,teachers,course_specific',
            'course_id' => 'required_if:invite_option,course_specific|exists:courses,id',
            'url' => 'required|url',
        ]);

        $zoomMeeting->update($request->all());

        return response()->json(['message' => 'Meeting URL updated successfully.']);
    }

    public function destroy(ZoomMeeting $zoomMeeting)
    {
        $zoomMeeting->delete();

        return response()->json(['message' => 'Meeting URL deleted successfully.']);
    }

    public function getMeetings(Request $request)
    {
        try {
            $zoomMeetings = ZoomMeeting::with(['user', 'course'])->get();
            return DataTables::of($zoomMeetings)
                ->addColumn('actions', function ($row) {
                    return view('dashboard.teacher.zoom_meetings.actions', compact('row'))->render();
                })
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings: ' . $e->getMessage()], 500);
        }
    }
}