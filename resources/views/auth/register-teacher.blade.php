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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phone_number");
            const countryCodeInput = document.querySelector("#country_code");
            const countryInput = document.querySelector("#country_location-input");
            const phoneError = document.querySelector("#error_phone_number");

            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "auto",
                nationalMode: true,
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

            iti.promise.then(function() {
                // Set initial country code value
                const selectedCountryData = iti.getSelectedCountryData();
                countryCodeInput.value = "+" + selectedCountryData.dialCode;
            });

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
                const countryName = countryData.name.split(" (")[0];
                countryInput.value = countryName;
                validatePhoneNumber();
            });

            function validatePasswords() {
                const passwordInput = document.querySelector("#password");
                const confirmPasswordInput = document.querySelector("#password_confirmation");
                const passwordError = document.querySelector("#error_password");
                const confirmPasswordError = document.querySelector("#error_password_confirmation");

                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password.length < 8) {
                    passwordError.textContent = "Password must be at least 8 characters.";
                    return false;
                } else {
                    passwordError.textContent = "";
                }

                if (password !== confirmPassword) {
                    confirmPasswordError.textContent = "Passwords do not match.";
                    return false;
                } else {
                    confirmPasswordError.textContent = "";
                }

                return true;
            }

            document.querySelector("#password").addEventListener("keyup", validatePasswords);
            document.querySelector("#password_confirmation").addEventListener("keyup", validatePasswords);

            document.querySelector("#registerForm").addEventListener("submit", function(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                fetch("{{ route('register-teacher') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.errors) {
                            // Empty the old errors
                            document.querySelectorAll('.text-danger').forEach(el => el.textContent =
                                '');

                            for (const [key, messages] of Object.entries(data.errors)) {
                                document.querySelector(`#error_${key}`).textContent = messages.join(
                                    ", ");
                            }
                        } else {
                            swal("Success!", data.message, "success");
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>

</x-guest-layout>
