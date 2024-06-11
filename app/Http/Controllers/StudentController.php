<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\UserMeeting;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('dashboard.student.dashboard', compact('courses'));
    }

    public function myMeetings()
    {
        return view('dashboard.student.meetings');
    }

    public function getMeetings(Request $request)
    {
        try {
            $user = Auth::user();
            $userMeetings = UserMeeting::where('user_id', $user->id)->with(['meeting', 'meeting.user'])->get();

            $dataTable = DataTables::of($userMeetings)
                ->editColumn('meeting.start_time', function ($row) {
                    return \Carbon\Carbon::parse($row->meeting->start_time)->format('d-m-Y / h:i A');
                })
                ->editColumn('meeting.duration', function ($row) {
                    $hours = intdiv($row->meeting->duration, 60);
                    $minutes = $row->meeting->duration % 60;
                    return $hours > 0
                        ? ($hours . ' hour' . ($hours > 1 ? 's' : '') . ($minutes > 0 ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : ''))
                        : $row->meeting->duration . ' minute' . ($row->meeting->duration > 1 ? 's' : '');
                })
                ->addColumn('teacher_name', function ($row) {
                    return $row->meeting->user->full_name;
                })
                ->addColumn('join_url', function ($row) {
                    return '<a href="' . $row->meeting->join_url . '" class="btn btn-primary" target="_blank">Join Meeting</a>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('student.meetings.show', $row->meeting_id) . '" class="btn btn-info">View Details</a>';
                })
                ->rawColumns(['join_url', 'actions']);

            return $dataTable->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings: ' . $e->getMessage()], 500);
        }
    }

    public function showMeeting($id)
    {
        $userMeeting = UserMeeting::where('user_id', Auth::id())->where('meeting_id', $id)->with(['meeting', 'meeting.user'])->firstOrFail();

        return view('dashboard.student.meeting-details', compact('userMeeting'));
    }
}