<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class ZoomMeetingController extends Controller
{
    protected $zoomAccountId;
    protected $zoomClientId;
    protected $zoomClientSecret;

    public function __construct()
    {
        // $this->zoomAccountId = env('ZOOM_ACCOUNT_ID');
        $this->zoomClientId = env('ZOOM_CLIENT_ID');
        $this->zoomClientSecret = env('ZOOM_CLIENT_SECRET');
    }

    private function getAccessToken()
    {
        try {
            $clientId = env('ZOOM_CLIENT_ID');
            $clientSecret = env('ZOOM_CLIENT_SECRET');

            if (!$clientId || !$clientSecret) {
                throw new \Exception('Missing Zoom API credentials.');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            ])->asForm()->post('https://zoom.us/oauth/token', [
                        'grant_type' => 'client_credentials',
                    ]);

            \Log::info('Zoom Token Response', ['response' => $response->json()]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            } else {
                $error = $response->json();
                throw new \Exception('Error retrieving access token: ' . json_encode($error));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve Zoom access token', ['error' => $e->getMessage()]);
            throw new \Exception('Unable to retrieve access token');
        }
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
        ]);

        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withToken($accessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
                'topic' => $request->topic,
                'type' => 2,
                'start_time' => $request->start_time,
                'duration' => $request->duration,
                'timezone' => 'UTC',
                'agenda' => $request->agenda,
            ]);

            \Log::info('Zoom Meeting Creation Response', ['response' => $response->json()]);

            if ($response->successful()) {
                $meeting = $response->json();
                $inviteOption = $request->invite_option;
                $invitedUsers = [];

                if ($inviteOption == 'all') {
                    $invitedUsers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })->get();
                } elseif ($inviteOption == 'teachers') {
                    $invitedUsers = User::whereHas('roles', function ($q) {
                        $q->where('name', 'teacher');
                    })->get();
                } elseif ($inviteOption == 'course_specific') {
                    $course = Course::findOrFail($request->course_id);
                    $invitedUsers = $course->students()->with('user')->get()->pluck('user');
                }

                ZoomMeeting::create([
                    'user_id' => Auth::id(),
                    'course_id' => $request->course_id,
                    'meeting_id' => $meeting['id'],
                    'topic' => $request->topic,
                    'agenda' => $request->agenda,
                    'start_time' => $request->start_time,
                    'duration' => $request->duration,
                    'password' => $meeting['password'],
                    'join_url' => $meeting['join_url'],
                ]);

                // Notify invited users
                foreach ($invitedUsers as $user) {
                    // Send notification to $user
                }

                return response()->json(['message' => 'Zoom meeting created successfully.']);
            } else {
                $error = $response->json();
                \Log::error('Failed to create Zoom meeting', ['error' => $error]);
                return response()->json(['message' => 'Failed to create Zoom meeting: ' . json_encode($error)], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create Zoom meeting', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to create Zoom meeting: ' . $e->getMessage()], 500);
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

        return view('dashboard.teacher.zoom_meetings.edit', compact('zoomMeeting', 'courses'));
    }

    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        $request->validate([
            'topic' => 'required',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
            'invite_option' => 'required|in:all,teachers,course_specific',
            'course_id' => 'required_if:invite_option,course_specific|exists:courses,id',
        ]);

        $zoomMeeting->update($request->all());

        return redirect()->route('zoom-meetings.index')->with('success', 'Zoom meeting updated successfully.');
    }

    public function destroy(ZoomMeeting $zoomMeeting)
    {
        $zoomMeeting->delete();

        return redirect()->route('zoom-meetings.index')->with('success', 'Zoom meeting deleted successfully.');
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