<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts',
            'phone' => 'nullable|string|max:15',
            'gender' => 'required|in:Male,Female,Other',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'additional_file' => 'nullable|file|max:5120',
            'custom_fields' => 'nullable|array',
        ]);

        // Handle file uploads
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $validated['additional_file'] = $request->file('additional_file')->store('files', 'public');
        }

        // Store data
        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact added successfully!');
    }

    public function show(Contact $contact)
    {
        return view('contacts.show', compact('contact'));
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email,' . $contact->id,
            'phone' => 'nullable|string|max:15',
            'gender' => 'required|in:Male,Female,Other',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'additional_file' => 'nullable|file|max:5120',
            'custom_fields.key' => 'nullable|array',
            'custom_fields.value' => 'nullable|array',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if ($contact->profile_image) {
                Storage::delete($contact->profile_image); // Delete old file
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        } else {
            $validated['profile_image'] = $contact->profile_image; // Keep existing image
        }

        // Handle additional file upload
        if ($request->hasFile('additional_file')) {
            if ($contact->additional_file) {
                Storage::delete($contact->additional_file); // Delete old file
            }
            $validated['additional_file'] = $request->file('additional_file')->store('files', 'public');
        } else {
            $validated['additional_file'] = $contact->additional_file; // Keep existing file
        }

        // Process custom fields
        $customFields = [];
        if (!empty($request->input('custom_fields.key'))) {
            foreach ($request->input('custom_fields.key') as $index => $key) {
                if (!empty($key)) {
                    $customFields[$key] = $request->input('custom_fields.value')[$index] ?? null;
                }
            }
        }
        $validated['custom_fields'] = json_encode($customFields, JSON_UNESCAPED_UNICODE);

        // Update contact
        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully!');
    }





    public function destroy(Contact $contact)
    {
        Storage::delete([$contact->profile_image, $contact->additional_file]);
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully!');
    }


public function filter(Request $request)
{
    $query = Contact::query();

    if ($request->has('search') && !empty($request->search)) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
    }

    if ($request->has('gender') && !empty($request->gender)) {
        $query->where('gender', $request->gender);
    }

    $contacts = $query->get();

    return response()->json($contacts);
}
    public function merge(Request $request)
    {
        $request->validate([
            'master_contact_id' => 'required|exists:contacts,id',
            'secondary_contact_id' => 'required|exists:contacts,id',
        ]);

        $masterContact = Contact::findOrFail($request->master_contact_id);
        $secondaryContact = Contact::findOrFail($request->secondary_contact_id);

        // Merge logic (e.g., update fields, transfer data, etc.)
        // Example: If a field in masterContact is empty, fill it with secondaryContact's data
        if (!$masterContact->phone) {
            $masterContact->phone = $secondaryContact->phone;
        }

        // Save merged contact
        $masterContact->save();

        // Delete the secondary contact
        $secondaryContact->delete();

        return response()->json(['success' => true, 'message' => 'Contacts merged successfully!']);
    }

}
