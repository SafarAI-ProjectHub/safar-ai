<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Chatify\Facades\ChatifyMessenger as Chatify;
use App\Models\ChMessage as Message;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\Models\Notification;
use App\Events\NotificationEvent;

class ContractController extends Controller
{
    /**
     * دالة عرض جميع العقود (للمسؤول Admin) وعرضها في جدول باستخدام Ajax و DataTables
     */
    public function index(Request $request)
    {
        // هنا نفحص إن كان الطلب من نوع Ajax
        if ($request->ajax()) {
            // نجلب جميع العقود مع بيانات المعلم المرتبط بالعقد
            $data = Contract::with('teacher')->get();

            // نستخدم DataTables لتنسيق البيانات للواجهة الأمامية
            return DataTables::of($data)
                // عمود يعرض اسم المعلم
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher->full_name;
                })
                // عمود لإظهار أزرار العرض والتعديل
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex justify-content-around gap-2">
                                <button class="btn btn-sm btn-primary view-contract" data-id="' . $row->id . '">View</button>
                                <button class="btn btn-sm btn-warning edit-contract" data-id="' . $row->id . '">Edit</button>
                            </div>';
                })
                // نسمح بعرض الأعمدة بصيغة HTML بدون ترميز
                ->rawColumns(['actions'])
                ->make(true);
        }

        // إذا لم يكن الطلب Ajax، يتم عرض صفحة العقود
        return view('dashboard.admin.contracts.index');
    }

    /**
     * دالة عرض تفاصيل عقد معين (تعيد البيانات على شكل JSON)
     */
    public function show($id)
    {
        // نجد العقد بناء على الـ id مع بيانات المعلم
        $contract = Contract::with('teacher')->findOrFail($id);

        // نعيد المعلومات بصيغة JSON لعرضها في واجهة المستخدم
        return response()->json([
            'teacher_name' => $contract->teacher->full_name,
            'other_party_name' => $contract->other_party_name,
            'salary' => $contract->salary,
            'salary_period' => $contract->salary_period,
            'status' => $contract->status,
            'signature' => $contract->signature,
            'contract_date' => $contract->contract_date,
            'contract_agreement' => $contract->contract_agreement,
            'employee_duties' => $contract->employee_duties,
            'responsibilities' => $contract->responsibilities,
            'employment_period' => $contract->employment_period,
            'compensation' => $contract->compensation,
            'legal_terms' => $contract->legal_terms
        ]);
    }

    /**
     * دالة عرض فورم إنشاء عقد جديد مع تحديد معلم معين
     */
    public function create($teacherId)
    {
        // نجلب بيانات المعلم حسب المعرف
        $teacher = User::findOrFail($teacherId);

        // نعرض صفحة إنشاء العقد مع تمرير بيانات المعلم
        return view('dashboard.admin.contracts.create', compact('teacher'));
    }

    /**
     * دالة حفظ عقد جديد في قاعدة البيانات
     */
    public function store(Request $request)
    {
        // هنا نجري التحقق (validation) على الحقول المطلوبة
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'other_party_name' => 'required|string|max:255',
            'salary' => [
                'required',
                'numeric',
                'regex:/^\d{1,18}(\.\d{1,2})?$/'
            ],
            'salary_period' => 'required|string|max:255',
            'contract_agreement' => 'required|string|max:255',
            'employee_duties' => 'required|string|max:255',
            'responsibilities' => 'required|string|max:255',
            'employment_period' => 'required|string|max:255',
            'compensation' => 'required|string|max:255',
            'legal_terms' => 'required|string|max:255',
        ]);

        // نقوم بإنشاء العقد وحفظه في قاعدة البيانات
        Contract::create($request->all());

        // نعيد رد مناسب (JSON) يؤكد نجاح العملية
        return response()->json(['message' => 'Contract created successfully.']);
    }

    /**
     * دالة عرض واجهة تعديل عقد معين
     */
    public function edit($contractId)
    {
        // نجلب العقد من قاعدة البيانات مع التحقق من وجوده
        $contract = Contract::findOrFail($contractId);

        // نجلب معرّف المعلم المرتبط بالعقد
        $teacher_id = $contract->teacher_id;

        // نفحص ما إذا كانت هناك رسالة سابقة مرتبطة بهذا العقد بين المعلم والمرسل (الادمن)
        $query = Message::where('contract_id', $contractId)->where('from_id', $teacher_id)->exists()
            ? Message::where('contract_id', $contractId)->where('from_id', $teacher_id)->first()
            : null;

        // إذا وجدنا رسالة سابقة، نأتي بالادمن من الرسالة
        if ($query) {
            $admin = User::find($query->to_id);
        } else {
            // إذا لم نجد رسالة سابقة، نجلب أول ادمن موجود
            $admin = User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->first();
        }

        // نجلب لون الماسنجر الخاص بالمستخدم الحالي (لعمل الثيم المناسب في الدردشة)
        $messenger_color = Auth::user()->messenger_color;

        // نعرض صفحة تعديل العقد مع تمرير البيانات اللازمة
        return view('dashboard.admin.contracts.edit', [
            'id' => $admin->id,
            'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
            'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
            'contract' => $contract,
            'teacher_id' => $teacher_id
        ]);
    }

    /**
     * دالة تحديث بيانات عقد محدد
     */
    public function update(Request $request, $contractId)
    {
        // نتحقق من صحة البيانات المدخلة
        $request->validate([
            'other_party_name' => 'required|string|max:255',
            'salary' => [
                'required',
                'numeric',
                'regex:/^\d{1,18}(\.\d{1,2})?$/'
            ],
            'salary_period' => 'required|string|max:255',
            'contract_agreement' => 'required|string|max:255',
            'employee_duties' => 'required|string|max:255',
            'responsibilities' => 'required|string|max:255',
            'employment_period' => 'required|string|max:255',
            'compensation' => 'required|string|max:255',
            'legal_terms' => 'required|string|max:255',
        ]);

        // نجلب العقد للتعديل
        $contract = Contract::findOrFail($contractId);

        // نقوم بتحديث بيانات العقد
        $contract->update($request->all());

        // نعيد رد يؤكد نجاح التحديث
        return response()->json(['message' => 'Contract updated successfully.']);
    }

    /*
     * ----------------------------------------------
     *                  Teacher functions
     * ----------------------------------------------
     */

    /**
     * دالة عرض العقد الخاص بالمعلم (في حالة كان المستخدم معلماً)
     */
    public function myContract()
    {
        // في حالة كان المستخدم Admin، نعيد توجيهه لصفحة العقود
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('contracts.index');
        }
        // في حالة كان المستخدم معلماً
        elseif (Auth::user()->hasRole('Teacher')) {
            // نجلب معرّف المستخدم الحالي
            $teacherId = Auth::id();

            // نجلب عقد المعلم من قاعدة البيانات
            $contract = Contract::where('teacher_id', $teacherId)->first();

            // إذا لم يكن هناك عقد، ننشئ كائن عقد جديد فارغ (لإظهاره في الواجهة فقط)
            if (!$contract) {
                $contract = new Contract();
            }

            // نجلب بيانات الادمن الأول (عادةً للرسائل والمحادثات)
            $admin = User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->first();

            // نجلب لون واجهة الدردشة للمستخدم الحالي
            $messenger_color = Auth::user()->messenger_color;

            // نعرض صفحة العقد الخاص بالمعلم
            return view('dashboard.teacher.contracts.my_contract', [
                'id' => Auth::id(),
                'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
                'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
                'contract' => $contract,
                'admin' => $admin
            ]);
        } else {
            // إذا لم يكن Admin أو Teacher، غير مصرح له
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * دالة توقيع العقد من جهة المعلم
     */
    public function signContract(Request $request)
    {
        // نتحقق من وجود العقد ومدخلات التوقيع
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'signature' => 'required|string|max:255',
        ]);

        // نجلب العقد المطلوب توقيعه
        $contract = Contract::findOrFail($request->contract_id);

        // نضيف التوقيع ونغير الحالة إلى "Approved" ونضيف تاريخ التوقيع
        $contract->signature = $request->signature;
        $contract->status = 'Approved';
        $contract->contract_date = now();
        $contract->save();

        // نجلب جميع الإدمن (Admin أو Super Admin) لإرسال الإشعارات
        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'Admin')
                ->orWhere('name', 'Super Admin');
        })->get();

        // إنشاء إشعار لكل أدمن
        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'title' => 'Teacher Contract',
                'message' => "The teacher {$contract->teacher->full_name} has signed the contract.",
                'icon' => 'bx bx-bell',
                'type' => 'contract-signed',
                'is_seen' => false,
                'model_id' => $contract->id,
                'reminder' => false,
                'reminder_time' => null,
            ]);

            // إطلاق حدث الإشعار ليتم استقباله في الواجهة (مثلاً باستخدام Pusher أو غيره)
            event(new NotificationEvent($notification));
        }

        // نعيد رد يؤكد نجاح التوقيع
        return response()->json(['message' => 'Contract signed successfully.']);
    }

    /**
     * دالة تنزيل العقد بصيغة PDF
     */
    public function downloadContractPDF($contractId)
    {
        // نجلب العقد مع بيانات المعلم
        $contract = Contract::with('teacher')->findOrFail($contractId);

        // إعداد البيانات المطلوبة في الـ PDF
        $data = [
            'contract' => $contract
        ];

        // ننشئ كائن PDF من خلال المنظومة
        $pdf = PDF::loadView('pdf.contract', $data);

        // نحدد حجم ورقة الطباعة
        $pdf->setPaper('A4');

        // نقوم بتنزيل الملف باسم مناسب يحتوي على معرّف العقد
        return $pdf->download('contract_' . $contract->id . '.pdf');
    }
}

