<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Support\Facades\Auth;

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
                                <button class="btn btn-primary view-contract" data-id="' . $row->id . '">View</button>
                                <button class="btn btn-warning edit-contract" data-id="' . $row->id . '">Edit</button>
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
        $contract = Contract::create($request->all());
        return response()->json(['message' => 'Contract created successfully.']);
    }

    public function edit($contractId)
    {
        $contract = Contract::findOrFail($contractId);
        return view('dashboard.admin.contracts.edit', compact('contract'));
    }

    public function update(Request $request, $contractId)
    {
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

            return view('dashboard.teacher.contracts.my_contract', compact('contract'));
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
        $contract->save();

        return response()->json(['message' => 'Contract signed successfully.']);
    }
}