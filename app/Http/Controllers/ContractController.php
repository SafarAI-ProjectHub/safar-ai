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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Contract::with('teacher')->get();
            return DataTables::of($data)
                ->addColumn('teacher_name', function ($row) {
                    return $row->teacher->full_name;
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex justify-content-around gap-2">
                                <button class="btn btn-sm btn-primary view-contract" data-id="' . $row->id . '">View</button>
                                <button class="btn btn-sm btn-warning edit-contract" data-id="' . $row->id . '">Edit</button>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('dashboard.admin.contracts.index');
    }

    public function show($id)
    {
        $contract = Contract::with('teacher')->findOrFail($id);
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

    public function create($teacherId)
    {
        $teacher = User::findOrFail($teacherId);
        return view('dashboard.admin.contracts.create', compact('teacher'));
    }

    public function store(Request $request)
    {
        request()->validate([
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
        $contract = Contract::create($request->all());
        return response()->json(['message' => 'Contract created successfully.']);
    }

    public function edit($contractId)
    {
        $contract = Contract::findOrFail($contractId);
        $teacher_id = $contract->teacher_id;
        $query = Message::where('contract_id', $contractId)->where('from_id', $teacher_id)->exists() ? Message::where('contract_id', $contractId)->where('from_id', $teacher_id)->first() : null;
        if ($query) {
            $admin = User::find($query->to_id);
        } else {
            $admin = User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->first();
        }
        $messenger_color = Auth::user()->messenger_color;

        return view('dashboard.admin.contracts.edit', [
            'id' => $admin->id,
            'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
            'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
            'contract' => $contract,
            'teacher_id' => $teacher_id
        ]);
    }

    public function update(Request $request, $contractId)
    {
        request()->validate([
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
        $contract = Contract::findOrFail($contractId);
        $contract->update($request->all());
        return response()->json(['message' => 'Contract updated successfully.']);
    }



    /*
     *
     * Teacher functions 
     *
     */


    public function myContract()
    {
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('contracts.index');
        } elseif (Auth::user()->hasRole('Teacher')) {


            $teacherId = Auth::id();
            $contract = Contract::where('teacher_id', $teacherId)->first();

            if (!$contract) {
                $contract = new Contract();
            }
            // return this with teh blade : 'id' => $id ?? 0,
            // 'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
            // 'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
            // $contract 
            $id = Auth::id();
            $admin = User::whereHas('roles', function ($q) {
                $q->where('name', 'Admin');
            })->first();
            $messenger_color = Auth::user()->messenger_color;

            return view('dashboard.teacher.contracts.my_contract', [
                'id' => $id,
                'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
                'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
                'contract' => $contract,
                'admin' => $admin
            ]);

        } else {
            abort(403, 'Unauthorized action.');
        }

    }

    public function signContract(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'signature' => 'required|string|max:255',
        ]);

        $contract = Contract::findOrFail($request->contract_id);
        $contract->signature = $request->signature;
        $contract->status = 'Approved';
        $contract->contract_date = now();
        $contract->save();
        $admins = User::whereHas('roles', function ($q) {
            $q->where('name', 'Admin')
                ->orWhere('name', 'Super Admin');
        })->get();

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

            event(new NotificationEvent($notification));

        }
        return response()->json(['message' => 'Contract signed successfully.']);
    }

    public function downloadContractPDF($contractId)
    {
        $contract = Contract::with('teacher')->findOrFail($contractId);

        $data = [
            'contract' => $contract
        ];

        $pdf = PDF::loadView('pdf.contract', $data);
        $pdf->setPaper('A4');

        return $pdf->download('contract_' . $contract->id . '.pdf');
    }

}