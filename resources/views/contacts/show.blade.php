@extends('layouts.app')

@section('content')

<h3>{{ $contact->name }}</h3>
<p>Email: {{ $contact->email }}</p>
<p>Phone: {{ $contact->phone }}</p>
<p>Gender: {{ $contact->gender }}</p>

@if($contact->profile_image)
    <p><img src="{{ asset('storage/' . $contact->profile_image) }}" width="100"></p>
@endif

<h4>Custom Fields</h4>

<ul>
    @php
        // Ensure custom fields are an array
        $customFields = is_array($contact->custom_fields) ? $contact->custom_fields : json_decode($contact->custom_fields, true);
    @endphp

    @if(!empty($customFields))
        @foreach($customFields as $key => $value)
            <li><strong>{{ $key }}</strong>: {{ is_array($value) ? implode(', ', $value) : $value }}</li>
        @endforeach
    @else
        <li>No custom fields available.</li>
    @endif
</ul>
<a href="{{ route('contacts.index') }}" class="btn btn-secondary mb-3">Back</a>

@endsection
