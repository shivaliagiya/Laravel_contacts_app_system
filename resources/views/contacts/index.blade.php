@extends('layouts.app')

@section('content')
    <a href="{{ url('contacts/create') }}" class="btn btn-primary mb-3">Add Contact</a>

    <!-- Merge Button -->
    <button id="mergeButton" class="btn btn-success mb-3" disabled>Merge Selected</button>

    <!-- Search and Filter Inputs -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" placeholder="Search by Name or Email" class="form-control">
        </div>
        <div class="col-md-6">
            <select id="genderFilter" class="form-control">
                <option value="">All Genders</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Select</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Profile Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="contactsTable">
        @foreach($contacts as $contact)
            <tr>
                <td><input type="checkbox" class="merge-checkbox" value="{{ $contact->id }}"></td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td>{{ $contact->phone }}</td>
                <td>{{ $contact->gender }}</td>
                <td>
                    @if($contact->profile_image)
                        <img src="{{ asset('storage/' . $contact->profile_image) }}" width="50" height="50">
                    @endif
                </td>
                <td class="actions">
                    <a href="{{ url('contacts/' . $contact->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ url('contacts/' . $contact->id . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ url('contacts/' . $contact->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Merge Modal -->
    <div id="mergeModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Master Contact</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Select the contact that should be kept as the main contact:</p>
                    <form id="mergeForm">
                        <input type="hidden" name="secondary_contact_id" id="secondaryContact">
                        <div id="masterContactOptions"></div>
                        <button type="submit" class="btn btn-success">Confirm Merge</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchInput");
            const genderFilter = document.getElementById("genderFilter");
            const mergeButton = document.getElementById("mergeButton");

            // Search and Filter Functionality
            function fetchFilteredContacts() {
                const searchQuery = searchInput.value.trim();
                const gender = genderFilter.value;

                fetch(`/contacts/filter?search=${searchQuery}&gender=${gender}`)
                    .then(response => response.json())
                    .then(data => {
                        const tbody = document.getElementById("contactsTable");
                        tbody.innerHTML = "";

                        data.forEach(contact => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                        <td><input type="checkbox" class="merge-checkbox" value="${contact.id}"></td>
                        <td>${contact.name}</td>
                        <td>${contact.email}</td>
                        <td>${contact.phone ?? ''}</td>
                        <td>${contact.gender}</td>
                        <td>
                            ${contact.profile_image ? `<img src="/storage/${contact.profile_image}" width="50" height="50">` : ''}
                        </td>
                        <td>
                            <a href="/contacts/${contact.id}" class="btn btn-info btn-sm">View</a>
                            <a href="/contacts/${contact.id}/edit" class="btn btn-warning btn-sm">Edit</a>
                            <form action="/contacts/${contact.id}" method="POST" style="display:inline;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    `;
                            tbody.appendChild(row);
                        });
                    })
                    .catch(error => console.error("Error fetching filtered contacts:", error));
            }

            searchInput.addEventListener("input", fetchFilteredContacts);
            genderFilter.addEventListener("change", fetchFilteredContacts);

            // Merge Functionality
            document.querySelectorAll(".merge-checkbox").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    const checked = document.querySelectorAll(".merge-checkbox:checked");
                    mergeButton.disabled = checked.length !== 2;
                });
            });

            mergeButton.addEventListener("click", function () {
                const checked = document.querySelectorAll(".merge-checkbox:checked");
                const ids = Array.from(checked).map(cb => cb.value);

                if (ids.length === 2) {
                    document.getElementById("secondaryContact").value = ids[1];

                    document.getElementById("masterContactOptions").innerHTML = `
                <label><input type="radio" name="master_contact_id" value="${ids[0]}" required> Contact ${ids[0]}</label><br>
                <label><input type="radio" name="master_contact_id" value="${ids[1]}" required> Contact ${ids[1]}</label>
            `;

                    var myModal = new bootstrap.Modal(document.getElementById('mergeModal'));
                    myModal.show();
                }
            });

            document.getElementById("mergeForm").addEventListener("submit", function (e) {
                e.preventDefault();

                const masterContactId = document.querySelector('input[name="master_contact_id"]:checked')?.value;
                const secondaryContactId = document.getElementById("secondaryContact").value;

                if (!masterContactId || !secondaryContactId) {
                    alert("Please select a master contact.");
                    return;
                }

                fetch('/contacts/merge', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        master_contact_id: masterContactId,
                        secondary_contact_id: secondaryContactId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Contacts merged successfully!');
                            location.reload();
                        } else {
                            alert('Merge failed: ' + (data.error || "Unknown error"));
                        }
                    })
                    .catch(error => console.error('Error merging contacts:', error));
            });
        });
    </script>

@endsection
