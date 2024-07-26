<x-guest-layout>
    <x-slot name="imageSlot">
        <img src="{{ asset('assets/images/login-images/register-cover.svg') }}" class="img-fluid auth-img-cover-login"
            width="650" alt="" />
    </x-slot>

    <form id="registerForm" method="POST" action="{{ route('register-teacher') }}" class="row g-3"
        enctype="multipart/form-data">
        @csrf

        <!-- First Name -->
        <div class="col-12">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}"
                required placeholder="John">
            <div class="text-danger" id="error_first_name"></div>
        </div>

        <!-- Last Name -->
        <div class="col-12">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}"
                required placeholder="Doe">
            <div class="text-danger" id="error_last_name"></div>
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                required placeholder="example@user.com">
            <div class="text-danger" id="error_email"></div>
        </div>

        <!-- Country Location (Hidden) -->
        <div class="col-12">
            <input type="hidden" name="country_location" id="country_location-input" value="">
        </div>

        <!-- Phone Number -->
        <div class="col-12">
            <label for="phone_number" class="form-label">Phone Number</label>
            <div class="input-group">
                <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                    value="{{ old('phone_number') }}" required placeholder="1234567890">
            </div>
            <div class="text-danger" id="error_phone_number"></div>
        </div>

        <!-- Date of Birth -->
        <div class="col-12">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                value="{{ old('date_of_birth') }}" required>
            <div class="text-danger" id="error_date_of_birth"></div>
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required
                placeholder="Enter Password">
            <div class="text-danger" id="error_password"></div>
        </div>

        <!-- Confirm Password -->
        <div class="col-12">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required
                placeholder="Confirm Password">
            <div class="text-danger" id="error_password_confirmation"></div>
        </div>

        <!-- CV Upload -->
        <div class="col-12">
            <label for="cv" class="form-label">Upload CV</label>
            <input type="file" class="form-control" id="cv" name="cv" required>
            <div class="text-danger" id="error_cv"></div>
        </div>

        <!-- Years of Experience -->
        <div class="col-12">
            <label for="years_of_experience" class="form-label">Years of Experience</label>
            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience"
                value="{{ old('years_of_experience') }}" required placeholder="Number of years">
            <div class="text-danger" id="error_years_of_experience"></div>
        </div>

        <!-- Terms and Conditions -->
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="terms" required>
                <label class="form-check-label" for="flexSwitchCheckChecked">I read and agree to Terms &
                    Conditions</label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign up</button>
            </div>
        </div>

        <!-- Sign In Link -->
        <div class="col-12 text-center">
            <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
        </div>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInputField = document.querySelector("#phone_number");
            const phoneErrorField = document.querySelector("#error_phone_number");
            const form = document.querySelector("#registerForm");

            const iti = window.intlTelInput(phoneInputField, {
                initialCountry: "auto",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                let isFormValid = validateForm();
                if (!isFormValid) {
                    alert('Please correct the errors before submitting the form.');
                    return;
                }

                let formData = new FormData(form);
                formData.append('country_code', iti.getSelectedCountryData().dialCode);

                fetch(form.getAttribute('action'), {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            // Handle form errors
                            Object.keys(data.errors).forEach(function(key) {
                                const errorDiv = document.querySelector('#error_' + key);
                                if (errorDiv) {
                                    errorDiv.textContent = data.errors[key][0];
                                }
                            });
                            alert('Please correct the errors and try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred, please try again.');
                    });
            });

            function validateForm() {
                let isValid = iti.isValidNumber();
                if (!isValid) {
                    const errorCode = iti.getValidationError();
                    let errorMessage = getPhoneError(errorCode);
                    phoneErrorField.textContent = errorMessage;
                } else {
                    phoneErrorField.textContent = '';
                }
                return isValid;
            }

            function getPhoneError(code) {
                switch (code) {
                    case intlTelInputUtils.validationError.INVALID_COUNTRY_CODE:
                        return "The country code is invalid.";
                    case intlTelInputUtils.validationError.TOO_SHORT:
                        return "The phone number is too short.";
                    case intlTelInputUtils.validationError.TOO_LONG:
                        return "The phone number is too long.";
                    case intlTelInputUtils.validationError.NOT_A_NUMBER:
                        return "The phone number is not a number.";
                    default:
                        return "The phone number is invalid.";
                }
            }
        });
    </script>
</x-guest-layout>
