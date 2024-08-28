<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Models\Course;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserMeeting;
use App\Events\NotificationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Zoom;

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
            'agenda' => 'nullable|string',
        ]);


        try {
            // Create Zoom meeting
            $meeting = Zoom::createMeeting([
                'agenda' => $request->input('agenda'),
                'topic' => $request->input('topic'),
                'type' => 2, // Scheduled meeting
                'duration' => $request->input('duration'),
                'timezone' => config('app.timezone'),
                'password' => '123456',
                'start_time' => $request->input('start_time'),
                // "schedule_for" => Auth::user()->email,
                'settings' => [
                    'join_before_host' => true,
                    'host_video' => true,
                    'participant_video' => true,
                    'mute_upon_entry' => true,
                    'waiting_room' => false,
                    'audio' => 'both',
                    'auto_recording' => 'none',
                    'approval_type' => 0,

                ],
            ]);

            $meetingData = $meeting['data'];

            // Store meeting in database
            $zoomMeeting = ZoomMeeting::create([
                'user_id' => Auth::id(),
                'course_id' => $request->course_id,
                'topic' => $request->topic,
                'agenda' => $request->agenda,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'meeting_id' => $meetingData['id'],
                'join_url' => $meetingData['join_url'],
            ]);

            $inviteOption = $request->input('invite_option');
            $invitedUsers = collect();

            if ($inviteOption == 'all') {
                if (Auth::user()->hasRole('Teacher')) {
                    $courses = Auth::user()->teacher->courses;


                    foreach ($courses as $course) {
                        $invitedUsers = $course->students()->with('user')->get()->pluck('user');
                    }

                } elseif (Auth::user()->hasRole('Admin|Super Admin')) {
                    $invitedUsers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })->get();
                } else {
                    abort(403);
                }
            } elseif ($inviteOption == 'teachers') {
                $invitedUsers = User::whereHas('roles', function ($q) {
                    $q->where('name', 'teacher');
                })->get();
            } elseif ($inviteOption == 'course_specific') {
                $course = Course::findOrFail($request->course_id);
                $invitedUsers = $course->students()->with('user')->get()->pluck('user');
            }

            foreach ($invitedUsers as $invitedUser) {



                $notification = Notification::create([
                    'user_id' => $invitedUser->id,
                    'title' => 'New Zoom Meeting',
                    'message' => "You have been invited to a Zoom meeting: " . $request->input('topic'),
                    'icon' => 'bx bx-video',
                    'type' => 'meeting',
                    'is_seen' => false,
                    'model_id' => $zoomMeeting->id,
                    'reminder' => false,
                    'reminder_time' => null,
                ]);

                UserMeeting::create([
                    'user_id' => $invitedUser->id,
                    'meeting_id' => $zoomMeeting->id,
                    'meeting_title' => $request->input('topic'),
                    'meeting_description' => $request->input('agenda'),
                    'meeting_time' => $request->input('start_time'),
                ]);

                event(new NotificationEvent($notification));
            }

            return response()->json(['message' => 'Meeting created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create meeting wertgr: ' . $e->getMessage()], 500);
        }
    }

    public function show(ZoomMeeting $zoomMeeting)
    {
        return response()->json($zoomMeeting);
    }

    public function edit(ZoomMeeting $zoomMeeting)
    {
        $user = Auth::user();
        $courses = [];

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = $user->teacher->courses;
        }

        return view('dashboard.teacher.create_zoom_meetings', ['zoomMeeting' => $zoomMeeting, 'courses' => $courses]);
    }

    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        $request->validate([
            'topic' => 'required',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
            'invite_option' => 'required|in:all,teachers,course_specific',
            'course_id' => 'required_if:invite_option,course_specific|exists:courses,id',
            'agenda' => 'nullable|string',
        ]);

        try {
            // Update Zoom meeting
            $meeting = Zoom::updateMeeting($zoomMeeting->meeting_id, [
                'agenda' => $request->input('agenda'),
                'topic' => $request->input('topic'),
                'type' => 2, // Scheduled meeting
                'duration' => $request->input('duration'),
                'timezone' => config('app.timezone'), // Set your timezone
                'password' => '123456',
                'start_time' => $request->input('start_time'),
                'settings' => [
                    'join_before_host' => true,
                    'host_video' => true,
                    'participant_video' => true,
                    'mute_upon_entry' => true,
                    'waiting_room' => false,
                    'audio' => 'both',
                    'auto_recording' => 'none',
                    'approval_type' => 0,
                ],
            ]);

            // Update meeting in database
            $zoomMeeting->update([
                'course_id' => $request->course_id,
                'topic' => $request->topic,
                'agenda' => $request->agenda,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                // 'join_url' => $meeting['data']['join_url'],
            ]);

            $inviteOption = $request->input('invite_option');
            $invitedUsers = collect();

            if ($inviteOption == 'all') {
                if (Auth::user()->hasRole('Teacher')) {
                    $courses = Auth::user()->teacher->courses;


                    foreach ($courses as $course) {
                        $invitedUsers = $course->students()->with('user')->get()->pluck('user');
                    }

                } elseif (Auth::user()->hasRole('Admin|Super Admin')) {
                    $invitedUsers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })->get();
                } else {
                    abort(403);
                }
            } elseif ($inviteOption == 'teachers') {
                if (Auth::user()->hasRole('Admin|Super Admin')) {
                    $invitedUsers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'teacher');
                    })->get();
                } else {
                    abort(403);
                }
            } elseif ($inviteOption == 'course_specific') {
                $course = Course::findOrFail($request->course_id);
                $invitedUsers = $course->students()->with('user')->get()->pluck('user');
            }

            foreach ($invitedUsers as $invitedUser) {
                $notification = Notification::create([
                    'user_id' => $invitedUser->id,
                    'title' => 'Updated Zoom Meeting',
                    'message' => "The Zoom meeting has been updated: " . $request->input('topic'),
                    'icon' => 'bx bx-video',
                    'type' => 'meeting',
                    'model_id' => $zoomMeeting->id,
                    'is_seen' => false,
                    'reminder' => false,
                    'reminder_time' => null,
                ]);

                UserMeeting::create([
                    'user_id' => $invitedUser->id,
                    'meeting_id' => $zoomMeeting->id,
                    'meeting_title' => $request->input('topic'),
                    'meeting_description' => $request->input('agenda'),
                    'meeting_time' => $request->input('start_time'),
                ]);

                event(new NotificationEvent($notification));
            }

            return response()->json(['message' => 'Meeting updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update meeting: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(ZoomMeeting $zoomMeeting)
    {
        try {
            // Delete Zoom meeting
            Zoom::deleteMeeting($zoomMeeting->meeting_id);

            // Delete meeting from database
            $zoomMeeting->delete();

            return response()->json(['message' => 'Meeting deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete meeting: ' . $e->getMessage()], 500);
        }
    }
    public function getMeetings(Request $request)
    {
        try {
            $user = Auth::user();
            $zoomMeetings = [];

            if ($user->hasRole('Admin|Super Admin')) {
                $zoomMeetings = ZoomMeeting::with(['user', 'course'])->get();
            } elseif ($user->hasRole('Teacher')) {
                $zoomMeetings = ZoomMeeting::where('user_id', $user->id)->with(['user', 'course'])->get();
            } else {
                abort(403);
            }

            $dataTable = DataTables::of($zoomMeetings)
                ->editColumn('start_time', function ($row) {
                    return \Carbon\Carbon::parse($row->start_time)->format('d-m-Y / h:i A');
                })
                ->editColumn('duration', function ($row) {
                    $hours = intdiv($row->duration, 60);
                    $minutes = $row->duration % 60;
                    return $hours > 0
                        ? ($hours . ' hour' . ($hours > 1 ? 's' : '') . ($minutes > 0 ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : ''))
                        : $row->duration . ' minute' . ($row->duration > 1 ? 's' : '');
                })
                ->addColumn('actions', function ($row) {
                    return '
                <a href="#" class="btn btn-sm btn-primary view-meeting" data-id="' . $row->id . '">View</a>
                <a href="' . route('zoom-meetings.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger delete-meeting" data-id="' . $row->id . '">Delete</button>
            ';
                })
                ->rawColumns(['actions']);

            if ($user->hasRole('Admin|Super Admin')) {
                $dataTable->addColumn('teacher_name', function ($row) {
                    return $row->user->full_name;
                });
            }

            return $dataTable->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings: ' . $e->getMessage()], 500);
        }
    }

}