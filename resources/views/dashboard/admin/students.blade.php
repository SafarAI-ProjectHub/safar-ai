@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Manage Students</h5>
            <div class="table-responsive">
                <table id="students-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Country</th>
                            <th>Age</th>
                            <th>English Proficiency Level</th>
                            <th>Subscription Status</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStudentForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div id="phone_error" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" id="country_location" name="country_location">
                        </div>
                        <div class="mb-3">
                            <label for="english_proficiency_level" class="form-label">English Proficiency Level</label>
                            <select class="form-select" id="english_proficiency_level" name="english_proficiency_level"
                                required>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subscription_status" class="form-label">Subscription Status</label>
                            <select class="form-select" id="subscription_status" name="subscription_status" required>
                                <option value="free">Free</option>
                                <option value="subscribed">Subscribed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>
    <script>
        const input = document.querySelector("#phone_number");
        const iti = window.intlTelInput(input, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script class="iti-load-utils" async="" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.students.data') }}',
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'age',
                        name: 'age'
                    },
                    {
                        data: 'english_proficiency_level',
                        name: 'english_proficiency_level'
                    },
                    {
                        data: 'subscription_status',
                        name: 'subscription_status'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary'
                    }
                ],

                lengthChange: false
            });



            // Handle Edit form submission
            $('#editStudentForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'PUT',
                    data: form.serialize(),
                    success: function(response) {
                        $('#editStudentModal').modal('hide');
                        table.ajax.reload();
                        Swal.fire('Success!', response.message, 'success');
                    },
                    error: function(response) {
                        Swal.fire('Error!', 'There was an error updating the student.',
                            'error');
                    }
                });
            });

            // Handle Delete button click
            $(document).on('click', '.delete-student', function() {
                var studentId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/student/' + studentId + '/delete',
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire('Error!',
                                    'There was an error deleting the student.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Phone number validation and country code update
            const phoneInput = document.querySelector("#phone_number");
            const countryCodeInput = document.querySelector("#country_code");
            const countryLocationInput = document.querySelector("#country_location");
            const phoneError = document.querySelector("#phone_error");

            // const iti = window.intlTelInput(phoneInput, {
            //     initialCountry: "auto",
            //     geoIpLookup: function(callback) {
            //         fetch('https://ipinfo.io?token=f77be74db12b48')
            //             .then(response => response.json())
            //             .then(data => {
            //                 const countryCode = (data && data.country) ? data.country : "us";
            //                 callback(countryCode);
            //             });
            //     },
            //     utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            // });
            // $("#phone_number").intlTelInput();

            function validatePhoneNumber() {
                const isValid = iti.isValidNumber();
                if (!isValid) {
                    const errorCode = iti.getValidationError();
                    let errorMessage = "Invalid phone number.";
                    switch (errorCode) {
                        case intlTelInputUtils.validationError.INVALID_NUMBER:
                            errorMessage = "The number entered is not valid.";
                            break;
                        case intlTelInputUtils.validationError.TOO_SHORT:
                            errorMessage = "The number entered is too short.";
                            break;
                        case intlTelInputUtils.validationError.TOO_LONG:
                            errorMessage = "The number entered is too long.";
                            break;
                        case intlTelInputUtils.validationError.INVALID_COUNTRY_CODE:
                            errorMessage = "The country code entered is invalid.";
                            break;
                    }
                    phoneError.textContent = errorMessage;
                } else {
                    phoneError.textContent = "";
                }
                return isValid;
            }

            phoneInput.addEventListener("keyup", validatePhoneNumber);

            phoneInput.addEventListener("countrychange", function() {
                const countryData = iti.getSelectedCountryData();
                countryCodeInput.value = "+" + countryData.dialCode;
                countryLocationInput.value = countryData.name.split(" (")[0];
                validatePhoneNumber();
            });
            // Handle Edit button click
            $(document).on('click', '.edit-student', function() {
                var studentId = $(this).data('id');
                $.get('/admin/student/' + studentId + '/edit', function(data) {

                    $('#editStudentForm').attr('action', '/admin/student/' + studentId + '/update');
                    $('#first_name').val(data.user.first_name);
                    $('#last_name').val(data.user.last_name);
                    $('#email').val(data.user.email);
                    $('#phone_number').val(data.user.phone_number);
                    $('#country_location').val(data.user.country_location);
                    $('#english_proficiency_level').val(data.english_proficiency_level);
                    $('#subscription_status').val(data.subscription_status);
                    $('#status').val(data.user.status);
                    iti.formatOnDisplay = true;
                    iti.setNumber(data.user.phone_number)

                    $('#editStudentModal').modal('show');
                });
            });
        });
    </script>
@endsection
