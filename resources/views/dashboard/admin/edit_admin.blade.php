@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

    <!-- يمكنك تخصيص نسخة SweetAlert2 أو إضافة CSS مخصص إذا رغبت -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Edit Admin</h5>
            <form id="admin-form" action="{{ route('admin.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- First Name -->
                <div class="form-group mb-4">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ old('first_name', $user->first_name) }}" required>
                    <div class="text-danger"></div>
                </div>

                <!-- Last Name -->
                <div class="form-group mb-4">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ old('last_name', $user->last_name) }}" required>
                    <div class="text-danger"></div>
                </div>

                <!-- Email -->
                <div class="form-group mb-4">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $user->email) }}" required>
                    <div class="text-danger"></div>
                </div>

                <!-- Phone Number -->
                <div class="form-group col-4 mb-4">
                    <label for="phone_number">Phone Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}" required>
                    </div>
                    <div id="phone_error" class="text-danger"></div>
                </div>

                <!-- Country Location (Hidden) -->
                <input type="hidden" id="country_location-input" name="country_location"
                       value="{{ old('country_location', $user->country_location) }}">

                <!-- Date of Birth -->
                <div class="form-group mb-4">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                           value="{{ $user->date_of_birth->format('Y-m-d') }}" required>
                    <div class="text-danger"></div>
                </div>

                <!-- Admin Status - ديناميكي -->
                <div class="form-group mb-4">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}"
                                @if(old('status', $user->status) === $statusOption) selected @endif>
                                {{ $statusOption }}
                            </option>
                        @endforeach
                    </select>
                    <div class="text-danger"></div>
                </div>

                <!-- Password (لـ Admin الذي نعدّله) -->
                <div class="form-group mb-4">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div id="password_error" class="text-danger"></div>
                </div>

                <!-- Confirm Password (لـ Admin الذي نعدّله) -->
                <div class="form-group mb-4">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    <div id="confirm_password_error" class="text-danger"></div>
                </div>

                <!-- Super Admin Password (للتأكيد) -->
                <div class="form-group mb-4">
                    <label for="super_admin_password">Super Admin Password</label>
                    <input type="password" class="form-control" id="super_admin_password" name="super_admin_password" required>
                    <div id="super_admin_password_error" class="text-danger"></div>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="button" id="submit-button" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- سكربت مكتبة intlTelInput -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>

    <!-- سكربت SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            const phoneInputField = $("#phone_number")[0];
            const countryCodeInput = $("#country_code");
            const countryLocationInput = $("#country_location-input");
            const phoneError = $("#phone_error");
            const passwordError = $("#password_error");
            const confirmPasswordError = $("#confirm_password_error");
            const superAdminPasswordError = $("#super_admin_password_error");

            const iti = window.intlTelInput(phoneInputField, {
                initialCountry: "auto",
                geoIpLookup: function(callback) {
                    fetch('https://ipinfo.io?token=f77be74db12b48')
                        .then(response => response.json())
                        .then(data => {
                            const countryCode = (data && data.country) ? data.country : "us";
                            callback(countryCode);
                        });
                },
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            function validatePhoneNumber() {
                if (!iti.isValidNumber()) {
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
                    phoneError.text(errorMessage);
                    return false;
                }
                phoneError.text("");
                return true;
            }

            function validatePasswords() {
                const password = $("#password").val();
                const confirmPassword = $("#password_confirmation").val();
                if (password.length > 0 && password.length < 8) {
                    passwordError.text("Password must be at least 8 characters.");
                    return false;
                }
                if (password !== confirmPassword) {
                    confirmPasswordError.text("Passwords do not match.");
                    return false;
                }
                passwordError.text("");
                confirmPasswordError.text("");
                return true;
            }

            phoneInputField.addEventListener("input", validatePhoneNumber);
            phoneInputField.addEventListener("countrychange", function() {
                const countryData = iti.getSelectedCountryData();
                countryCodeInput.val("+" + countryData.dialCode);
                countryLocationInput.val(countryData.name.split(" (")[0]);
                validatePhoneNumber();
            });

            $("#submit-button").click(function() {
                const isPhoneValid = validatePhoneNumber();
                const arePasswordsValid = validatePasswords();

                if (isPhoneValid && arePasswordsValid) {
                    $.ajax({
                        url: $("#admin-form").attr('action'),
                        type: 'POST',
                        data: $("#admin-form").serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'success',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "{{ route('admin.list') }}";
                            });
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON.errors;

                            // مسح رسائل الخطأ السابقة
                            $("#first_name").next('.text-danger').text('');
                            $("#last_name").next('.text-danger').text('');
                            $("#email").next('.text-danger').text('');
                            $("#phone_number").next('.text-danger').text('');
                            $("#date_of_birth").next('.text-danger').text('');
                            $("#status").next('.text-danger').text('');
                            passwordError.text('');
                            confirmPasswordError.text('');
                            superAdminPasswordError.text('');

                            if (errors) {
                                if (errors.first_name) {
                                    $("#first_name").next('.text-danger').text(errors.first_name[0]);
                                }
                                if (errors.last_name) {
                                    $("#last_name").next('.text-danger').text(errors.last_name[0]);
                                }
                                if (errors.email) {
                                    $("#email").next('.text-danger').text(errors.email[0]);
                                }
                                if (errors.phone_number) {
                                    $("#phone_number").next('.text-danger').text(errors.phone_number[0]);
                                }
                                if (errors.date_of_birth) {
                                    $("#date_of_birth").next('.text-danger').text(errors.date_of_birth[0]);
                                }
                                if (errors.status) {
                                    $("#status").next('.text-danger').text(errors.status[0]);
                                }
                                if (errors.password) {
                                    passwordError.text(errors.password[0]);
                                }
                                if (errors.super_admin_password) {
                                    superAdminPasswordError.text(errors.super_admin_password[0]);
                                }
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'error',
                        text: 'Please correct the errors before submitting the form.'
                    });
                }
            });

            // تعيين كود الدولة والموقع مبدئيًا عند تحميل الصفحة
            const initialCountryData = iti.getSelectedCountryData();
            countryCodeInput.val("+" + initialCountryData.dialCode);
            countryLocationInput.val(initialCountryData.name.split(" (")[0]);

            // تعيين الدولة بناءً على رقم الهاتف الموجود مسبقًا
            iti.setNumber('{{ old('phone_number', $user->phone_number) }}');
        });
    </script>
@endsection
