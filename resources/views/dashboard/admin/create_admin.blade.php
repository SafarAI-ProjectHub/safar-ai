@extends('layouts_dashboard.main')

@section('styles')
    {{-- مكتبة intlTelInput --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    {{-- مكتبة SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Create Admin</h5>
            <form id="admin-form" action="{{ route('admin.store') }}" method="POST">
                @csrf

                <!-- First Name -->
                <div class="form-group mb-4">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ old('first_name') }}" required>
                    <div class="text-danger"></div>
                </div>
                <!-- Last Name -->
                <div class="form-group mb-4">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ old('last_name') }}" required>
                    <div class="text-danger"></div>
                </div>
                <!-- Email -->
                <div class="form-group mb-4">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                           required>
                    <div class="text-danger"></div>
                </div>
                <!-- Phone Number -->
                <div class="form-group col-4 mb-4">
                    <label for="phone_number">Phone Number</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div id="phone_error" class="text-danger"></div>
                </div>
                <!-- Country Location (Hidden) -->
                <input type="hidden" id="country_location-input" name="country_location" value="">
                <!-- Date of Birth -->
                <div class="form-group mb-4">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth') }}" required>
                    <div class="text-danger"></div>
                </div>
                <!-- Password -->
                <div class="form-group mb-4">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div id="password_error" class="text-danger"></div>
                </div>
                <!-- Confirm Password -->
                <div class="form-group mb-4">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                           required>
                    <div id="confirm_password_error" class="text-danger"></div>
                </div>
                <!-- Submit Button -->
                <div class="form-group">
                    <button type="button" id="submit-button" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- مكتبة intlTelInput --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
    {{-- مكتبة SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const phoneInputField = $("#phone_number")[0];
            const countryCodeInput = $("#country_code");
            const countryLocationInput = $("#country_location-input");
            const phoneError = $("#phone_error");
            const passwordError = $("#password_error");
            const confirmPasswordError = $("#confirm_password_error");

            const iti = window.intlTelInput(phoneInputField, {
                initialCountry: "us",
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
                if (password.length < 8) {
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

            // ضبط القيمة المبدئية للدولة ورمز الاتصال
            const initialCountryData = iti.getSelectedCountryData();
            countryCodeInput.val("+" + initialCountryData.dialCode);
            countryLocationInput.val(initialCountryData.name.split(" (")[0]);

            $("#submit-button").click(function() {
                const isPhoneValid = validatePhoneNumber();
                const arePasswordsValid = validatePasswords();

                if (isPhoneValid && arePasswordsValid) {
                    $.ajax({
                        url: $("#admin-form").attr('action'),
                        type: 'POST',
                        data: $("#admin-form").serialize(),
                        success: function(response) {
                            // تنبيه SweetAlert عند النجاح
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "{{ route('admin.list') }}";
                                }
                            });
                        },
                        error: function(xhr) {
                            // عرض الأخطاء أسفل الحقول
                            const errors = xhr.responseJSON.errors;
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
                                    // phone_number
                                    $("#phone_number").next('.text-danger').text(errors.phone_number[0]);
                                }
                                if (errors.date_of_birth) {
                                    $("#date_of_birth").next('.text-danger').text(errors.date_of_birth[0]);
                                }
                                if (errors.password) {
                                    passwordError.text(errors.password[0]);
                                }
                            }
                        }
                    });
                } else {
                    // تنبيه SweetAlert عند وجود أخطاء في الحقول
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please correct the errors before submitting the form.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
@endsection
