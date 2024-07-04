<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactForm;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Create a new contact form entry
        ContactForm::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
        ]);


        return response('OK', 200);
    }

    public function index()
    {
        return view('dashboard.admin.contact_forms');
    }

    public function getContactForms(Request $request)
    {
        // Using DataTables DESC
        $contactForms = ContactForm::query()->orderBy('created_at', 'DESC');

        if ($request->has('resolved') && $request->resolved == 'all') {
            $contactForms;
        } elseif ($request->has('resolved')) {
            $contactForms->where('resolved', $request->resolved);
        }

        return DataTables::of($contactForms)
            ->addColumn('action', function ($contactForm) {
                if ($contactForm->resolved)
                    return '<dev class="btn-group d-flex justify-content-around">
                         
                        <button class="btn btn-danger btn-sm delete-contact" data-id="' . $contactForm->id . '">Delete</button>
                    </dev>
                    ';
                else {
                    return '<dev class="btn-group d-flex justify-content-around">
                    <a href="mailto:' . $contactForm->email . '?subject=Re:' . $contactForm->subject . '" class="btn btn-primary btn-sm">Respond</a>
                    <button class="btn btn-success btn-sm handle-contact" data-id="' . $contactForm->id . '">Mark as Resolved</button>
                    <button class="btn btn-danger btn-sm delete-contact" data-id="' . $contactForm->id . '">Delete</button>
                    </dev>
                ';
                }
            })
            ->make(true);
    }

    public function markAsResolved($id)
    {
        if (!ContactForm::find($id)) {
            return response()->json(['message' => 'Contact form not found.'], 404);
        }

        $contactForm = ContactForm::findOrFail($id);
        $contactForm->resolved = true;
        $contactForm->save();

        return response()->json(['message' => 'Contact form marked as resolved.'], 200);
    }

    public function destroy($id)
    {
        if (!ContactForm::find($id)) {
            return response()->json(['message' => 'Contact form not found.'], 404);
        }

        ContactForm::destroy($id);
        return response()->json(['message' => 'Contact form deleted.'], 200);
    }
}