<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $offers = Offer::all();
            return DataTables::of($offers)
                ->addColumn('actions', function ($offer) {
                    return view('dashboard.admin.partials.offer_actions', compact('offer'));
                })
                ->make(true);
        }

        return view('dashboard.admin.offers');
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'action_type' => 'required|in:link,email',
            'action_value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'background_image' => 'required|image|max:2048',
            'alignment' => 'required|in:left,right,center',
        ]);

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('background_images', 'public');
            $validatedData['background_image'] = $path;
        }

        $validatedData['user_id'] = auth()->id();

        Offer::create($validatedData);

        return response()->json(['success' => 'Offer created successfully']);
    }

    public function show($id)
    {
        $offer = Offer::findOrFail($id);
        return response()->json($offer);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required',
            'action_type' => 'required|in:link,email',
            'action_value' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'background_image' => 'nullable|image|max:2048',
            'alignment' => 'required|in:left,right,center',
        ]);

        $offer = Offer::findOrFail($id);

        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('background_images', 'public');
            $validatedData['background_image'] = $path;
        }

        $offer->update($validatedData);

        return response()->json(['success' => 'Offer updated successfully']);
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return response()->json(['success' => 'Offer deleted successfully']);
    }

    public function toggle($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->is_active = !$offer->is_active;
        $offer->save();

        return response()->json(['success' => 'Offer status updated successfully']);
    }
}