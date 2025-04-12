@extends('layouts.app')

@section('content')

<form action="{{ url('contacts') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control">
    </div>

    <div class="mb-3">
        <label>Gender</label><br>
        <input type="radio" name="gender" value="Male"> Male
        <input type="radio" name="gender" value="Female"> Female
        <input type="radio" name="gender" value="Other"> Other
    </div>

    <div class="mb-3">
        <label>Profile Image</label>
        <input type="file" name="profile_image" class="form-control">
    </div>

    <div class="mb-3">
        <label>Additional File</label>
        <input type="file" name="additional_file" class="form-control">
    </div>

    <h4>Custom Fields</h4>
    <div id="custom-fields">
        <div class="mb-3">
            <input type="text" name="custom_fields[key][]" placeholder="Field Name" class="form-control">
            <input type="text" name="custom_fields[value][]" placeholder="Field Value" class="form-control mt-2">
        </div>
    </div>

    <button type="button" id="add-field" class="btn btn-secondary">Add Field</button>

    <button type="submit" class="btn btn-success">Save Contact</button>

    <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Back</a>

</form>

<script>
    document.getElementById('add-field').addEventListener('click', function() {
        let div = document.createElement('div');
        div.classList.add('mb-3');
        div.innerHTML = '<input type="text" name="custom_fields[key][]" placeholder="Field Name" class="form-control">' +
                        '<input type="text" name="custom_fields[value][]" placeholder="Field Value" class="form-control mt-2">';
        document.getElementById('custom-fields').appendChild(div);
    });
</script>
@endsection
