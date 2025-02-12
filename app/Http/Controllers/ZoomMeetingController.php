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
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Zoom;

class ZoomMeetingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * عرض صفحة قائمة الاجتماعات (Datatable)
     */
    public function index()
    {
        return view('dashboard.teacher.zoom_meeting');
    }

    /**
     * صفحة إنشاء اجتماع جديد
     */
    public function create()
    {
        $user = Auth::user();
        $courses = [];

        // جلب الكورسات حسب صلاحيات المستخدم
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = $user->teacher->courses;
        }

        return view('dashboard.teacher.create_zoom_meetings', compact('courses'));
    }

    /**
     * إنشاء اجتماع زوم جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'topic'          => 'required',
            'start_time'     => 'required|date',
            'duration'       => 'required|integer|min:1',
            'invite_option'  => 'required|in:all,teachers,course_specific',
            'course_id'      => 'required_if:invite_option,course_specific|exists:courses,id',
            'agenda'         => 'nullable|string',
        ]);

        try {
            // يمكن استخدام Carbon للتنسيق الصحيح
            // Zoom يفضّل تنسيق ISO8601 مثل 2025-03-05T10:00:00
            $startTime = Carbon::parse($request->input('start_time'))
                               ->format('Y-m-d\TH:i:s');

            // إنشاء اجتماع زوم
            $meeting = Zoom::createMeeting([
                'topic'     => $request->input('topic'),
                'type'      => 2, // Scheduled meeting
                'duration'  => $request->input('duration'),
                'agenda'    => $request->input('agenda'),
                'timezone'  => config('app.timezone'), // أو أي تايم زون تشائين
                'password'  => '123456',
                'start_time'=> $startTime,
                'settings'  => [
                    'join_before_host'   => true,
                    'host_video'         => true,
                    'participant_video'  => true,
                    'mute_upon_entry'    => true,
                    'waiting_room'       => false,
                    'audio'              => 'both',
                    'auto_recording'     => 'none',
                    'approval_type'      => 0, // 0 = لا حاجة للموافقة, يمكن تغييرها
                ],
            ]);

            // نأخذ الـdata من الاستجابة
            // (حسب الحزمة، قد تكون الصيغة مختلفة)
            $meetingData = $meeting['data']; 
            // لو كانت حزمة أخرى، قد يكون $meeting->id أو $meeting->join_url إلخ

            // تخزين الاجتماع في قاعدة البيانات
            $zoomMeeting = ZoomMeeting::create([
                'user_id'    => Auth::id(),
                'course_id'  => $request->course_id,
                'topic'      => $request->topic,
                'agenda'     => $request->agenda,
                'start_time' => $request->start_time, // خزنيها بصيغة datetime
                'duration'   => $request->duration,
                'meeting_id' => $meetingData['id'],
                'join_url'   => $meetingData['join_url'],
            ]);

            // جلب المستخدمين المدعوين
            $invitedUsers = $this->getInvitedUsers($request);

            // إرسال إشعارات وتخزين UserMeeting
            $this->notifyAndAttachUsers($invitedUsers, $zoomMeeting, $request->topic, $request->agenda, $request->start_time);

            return response()->json(['message' => 'Meeting created successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create Zoom meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض بيانات الاجتماع (مثلاً للعرض في مودال)
     */
    public function show(ZoomMeeting $zoomMeeting)
    {
        return response()->json($zoomMeeting);
    }

    /**
     * صفحة تعديل الاجتماع
     */
    public function edit(ZoomMeeting $zoomMeeting)
    {
        $user = Auth::user();
        $courses = [];

        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = $user->teacher->courses;
        }

        return view('dashboard.teacher.create_zoom_meetings', [
            'zoomMeeting' => $zoomMeeting,
            'courses'     => $courses
        ]);
    }

    /**
     * تحديث اجتماع زوم
     */
    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        $request->validate([
            'topic'         => 'required',
            'start_time'    => 'required|date',
            'duration'      => 'required|integer|min:1',
            'invite_option' => 'required|in:all,teachers,course_specific',
            'course_id'     => 'required_if:invite_option,course_specific|exists:courses,id',
            'agenda'        => 'nullable|string',
        ]);

        try {
            $startTime = Carbon::parse($request->input('start_time'))
                               ->format('Y-m-d\TH:i:s');

            // تحديث اجتماع Zoom عبر الحزمة
            $updatedMeeting = Zoom::updateMeeting($zoomMeeting->meeting_id, [
                'topic'      => $request->input('topic'),
                'type'       => 2,
                'duration'   => $request->input('duration'),
                'agenda'     => $request->input('agenda'),
                'timezone'   => config('app.timezone'),
                'password'   => '123456',
                'start_time' => $startTime,
                'settings' => [
                    'join_before_host'   => true,
                    'host_video'         => true,
                    'participant_video'  => true,
                    'mute_upon_entry'    => true,
                    'waiting_room'       => false,
                    'audio'              => 'both',
                    'auto_recording'     => 'none',
                    'approval_type'      => 0,
                ],
            ]);

            // بإمكانك جلب أي بيانات جديدة إن توفرت
            // $updatedData = $updatedMeeting['data'] ?? [];

            // تحديث في قاعدة البيانات
            $zoomMeeting->update([
                'course_id'  => $request->course_id,
                'topic'      => $request->topic,
                'agenda'     => $request->agenda,
                'start_time' => $request->start_time,
                'duration'   => $request->duration,
                // إذا كنتِ ترغبين بتحديث join_url أو غيره
                // 'join_url'   => $updatedData['join_url'] ?? $zoomMeeting->join_url,
            ]);

            // تكرار منطق الدعوة/الإشعار
            $invitedUsers = $this->getInvitedUsers($request);
            $this->notifyAndAttachUsers($invitedUsers, $zoomMeeting, $request->topic, $request->agenda, $request->start_time, 'Updated Zoom Meeting');

            return response()->json(['message' => 'Meeting updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update meeting: ' . $e->getMessage()], 500);
        }
    }

    /**
     * حذف اجتماع زوم
     */
    public function destroy(ZoomMeeting $zoomMeeting)
    {
        try {
            // أولاً: حذف الاجتماع من Zoom
            Zoom::deleteMeeting($zoomMeeting->meeting_id);

            // ثم حذف السجل من DB
            $zoomMeeting->delete();

            return response()->json(['message' => 'Meeting deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete meeting: ' . $e->getMessage()], 500);
        }
    }

    /**
     * جلب الاجتماعات بشكل Ajax لـ DataTables
     */
    public function getMeetings(Request $request)
    {
        try {
            $user = Auth::user();
            $zoomMeetings = collect();

            if ($user->hasRole('Admin|Super Admin')) {
                $zoomMeetings = ZoomMeeting::with(['user', 'course'])->get();
            } elseif ($user->hasRole('Teacher')) {
                $zoomMeetings = ZoomMeeting::where('user_id', $user->id)
                                           ->with(['user', 'course'])
                                           ->get();
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
                    if ($hours > 0) {
                        $hourText = $hours . ' hour' . ($hours > 1 ? 's' : '');
                        $minuteText = $minutes > 0
                            ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '')
                            : '';
                        return $hourText . $minuteText;
                    } else {
                        return $row->duration . ' minute' . ($row->duration > 1 ? 's' : '');
                    }
                })
                ->addColumn('actions', function ($row) {
                    return '
                        <a href="#" class="btn btn-sm btn-primary view-meeting" data-id="' . $row->id . '">View</a>
                        <a href="' . route('zoom-meetings.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-meeting" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['actions']);

            // عمود إضافي لاسم المعلم إذا كان Admin
            if ($user->hasRole('Admin|Super Admin')) {
                $dataTable->addColumn('teacher_name', function ($row) {
                    return optional($row->user)->full_name;
                });
            }

            return $dataTable->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings: ' . $e->getMessage()], 500);
        }
    }

    /**
     * دالة خاصة لجلب قائمة المستخدمين الذين سيُدعون للاجتماع
     */
    private function getInvitedUsers(Request $request): Collection
    {
        $inviteOption = $request->input('invite_option');
        $invitedUsers = collect();

        if ($inviteOption === 'all') {
            if (Auth::user()->hasRole('Teacher')) {
                // كل طلاب الكورسات الخاصة بهذا المعلم
                $courses = Auth::user()->teacher->courses;
                foreach ($courses as $course) {
                    $students = $course->students()->with('user')->get()->pluck('user');
                    $invitedUsers = $invitedUsers->merge($students);
                }
            } elseif (Auth::user()->hasAnyRole(['Admin', 'Super Admin'])) {
                // جميع المستخدمين ذوي الدور Student
                $students = User::whereHas('roles', fn($q) => $q->where('name', 'student'))->get();
                $invitedUsers = $invitedUsers->merge($students);
            } else {
                abort(403);
            }
        } elseif ($inviteOption === 'teachers') {
            // جميع المعلمين
            $teachers = User::whereHas('roles', fn($q) => $q->where('name', 'teacher'))->get();
            $invitedUsers = $invitedUsers->merge($teachers);
        } elseif ($inviteOption === 'course_specific') {
            $course = Course::findOrFail($request->course_id);
            // جلب طلاب الكورس المحدد
            $students = $course->students()->with('user')->get()->pluck('user');
            $invitedUsers = $invitedUsers->merge($students);
        }

        // تخلّصي من أي تكرار (لو وجد) عبر unique
        return $invitedUsers->unique('id');
    }

    /**
     * دالة لإرسال إشعارات وإنشاء UserMeeting
     */
    private function notifyAndAttachUsers(Collection $invitedUsers, ZoomMeeting $zoomMeeting, string $topic, ?string $agenda, $startTime, string $notificationTitle = 'New Zoom Meeting')
    {
        foreach ($invitedUsers as $invitedUser) {
            $notification = Notification::create([
                'user_id'   => $invitedUser->id,
                'title'     => $notificationTitle,
                'message'   => "You have been invited to a Zoom meeting: {$topic}",
                'icon'      => 'bx bx-video',
                'type'      => 'meeting',
                'is_seen'   => false,
                'model_id'  => $zoomMeeting->id,
                'reminder'  => false,
                'reminder_time' => null,
            ]);

            UserMeeting::create([
                'user_id'            => $invitedUser->id,
                'meeting_id'         => $zoomMeeting->id,
                'meeting_title'      => $topic,
                'meeting_description'=> $agenda,
                'meeting_time'       => $startTime,
            ]);

            event(new NotificationEvent($notification));
        }
    }
}
