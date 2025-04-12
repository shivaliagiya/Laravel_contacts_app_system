@extends('layouts.app')

@section('content')
<form action="{{ route('contacts.update', $contact->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" value="{{ old('name', $contact->name) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $contact->email) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $contact->phone) }}" class="form-control">
    </div>

    <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-control" required>
            <option value="Male" {{ $contact->gender == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ $contact->gender == 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other" {{ $contact->gender == 'Other' ? 'selected' : '' }}>Other</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Profile Image</label>
        @if($contact->profile_image)
            <div><img src="{{ asset('storage/' . $contact->profile_image) }}" width="100"></div>
        @endif
        <input type="file" name="profile_image" class="form-control">
        <input type="hidden" name="existing_profile_image" value="{{ $contact->profile_image }}">
    </div>

    <div class="mb-3">
        <label>Additional File</label>
        @if($contact->additional_file)
            <div><a href="{{ asset('storage/' . $contact->additional_file) }}" target="_blank">View File</a></div>
        @endif
        <input type="file" name="additional_file" class="form-control">
        <input type="hidden" name="existing_additional_file" value="{{ $contact->additional_file }}">
    </div>

    <h4>Custom Fields</h4>
    <div id="custom-fields">
        @php
            $customFields = is_array($contact->custom_fields) ? $contact->custom_fields : json_decode($contact->custom_fields, true);
        @endphp

        @if(!empty($customFields))
            @foreach($customFields as $key => $value)
                <div class="mb-3">
                    <input type="text" name="custom_fields[key][]" value="{{ old('custom_fields.key.' . $loop->index, $key) }}" class="form-control" placeholder="Field Name">
                    <input type="text" name="custom_fields[value][]" value="{{ old('custom_fields.value.' . $loop->index, is_array($value) ? implode(', ', $value) : $value) }}" class="form-control mt-2" placeholder="Field Value">
                </div>
            @endforeach
        @else
            <p>No custom fields available.</p>
        @endif
    </div>

    <button type="submit" class="btn btn-warning">Update Contact</button>
    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back</a>

</form>
@endsection
