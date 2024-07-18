<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\ContractRule;
use App\Models\User;
use DataTables;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contracts = Contract::with('teacher');
            return DataTables::of($contracts)
                ->addColumn('teacher_name', function ($contract) {
                    return $contract->teacher->name;
                })
                ->addColumn('actions', function ($contract) {
                    return '
                        <button class="btn btn-info btn-sm" onclick="viewContract(' . $contract->id . ')">View</button>
                        <button class="btn btn-warning btn-sm" onclick="editContract(' . $contract->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteContract(' . $contract->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('dashboard.admin.contracts.index');
    }

    public function create(Request $request)
    {
        $teachers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teacher');
        })->get();

        $selectedTeacher = $request->teacher_id ? User::find($request->teacher_id) : null;
        return view('dashboard.admin.contracts.create', compact('teachers', 'selectedTeacher'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'other_party_name' => 'required|string|max:255',
            'contract_date' => 'required|date',
            'salary' => 'required|numeric',
            'rules' => 'required|array',
            'rules.*' => 'required|string|max:255'
        ]);

        $data = $request->only(['teacher_id', 'other_party_name', 'contract_date', 'salary']);

        $contract = Contract::create($data);

        foreach ($request->rules as $rule) {
            ContractRule::create(['contract_id' => $contract->id, 'rule' => $rule]);
        }

        return response()->json(['status' => 'Contract created successfully.', 'contract' => $contract]);
    }

    public function show(Contract $contract)
    {
        $contract->load('rules');
        return response()->json($contract);
    }

    public function edit(Contract $contract)
    {
        $contract->load('rules');
        $teachers = User::role('Teacher')->get();
        return view('dashboard.admin.contracts.edit', compact('contract', 'teachers'));
    }

    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'other_party_name' => 'required|string|max:255',
            'contract_date' => 'required|date',
            'salary' => 'required|numeric',
            'rules' => 'required|array',
            'rules.*' => 'required|string|max:255'
        ]);

        $data = $request->only(['other_party_name', 'contract_date', 'salary']);
        $data['company_name'] = 'Your Company Name';
        $data['company_logo'] = 'path/to/your/company/logo.png';

        $contract->update($data);
        $contract->rules()->delete();

        foreach ($request->rules as $rule) {
            ContractRule::create(['contract_id' => $contract->id, 'rule' => $rule]);
        }

        return response()->json(['status' => 'Contract updated successfully.', 'contract' => $contract]);
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return response()->json(['status' => 'Contract deleted successfully.']);
    }

    public function sign(Contract $contract)
    {
        $contract->load('rules');
        return view('dashboard.teacher.contracts.sign', compact('contract'));
    }

    public function storeSignature(Request $request, Contract $contract)
    {
        $request->validate([
            'signature' => 'required|string|max:255'
        ]);

        $contract->update(['signature' => $request->signature, 'status' => 'Completed']);

        return response()->json(['status' => 'Contract signed successfully.']);
    }
}