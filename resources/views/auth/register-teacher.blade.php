<x-guest-layout>
    <x-slot name="imageSlot">
        <img src="{{ asset('assets/images/login-images/register-cover.svg') }}" class="img-fluid auth-img-cover-login"
            width="650" alt="" />
    </x-slot>

    <form method="POST" action="{{ route('register-teacher') }}" class="row g-3" enctype="multipart/form-data">
        @csrf

        <!-- First Name -->
        <div class="col-12">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}"
                required placeholder="John">
            @error('first_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="col-12">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}"
                required placeholder="Doe">
            @error('last_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                required placeholder="example@user.com">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Country -->
        <div class="col-12">
            {{-- <label for="country_location" class="form-label">Country</label> --}}
            <input type="hidden" name="country_location" id="country_location-input" value="">
            {{-- <select class="form-control" id="country_location" name="country_location" required disabled>
                <!-- Options will be populated by JavaScript -->
            </select> --}}
            @error('country_location')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Phone Number -->
        <div class="col-12">
            <label for="phone_number" class="form-label">Phone Number</label>
            <div class="input-group">
                <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                    value="{{ old('phone_number') }}" required placeholder="1234567890">
            </div>
            <div id="phone_error" class="text-danger"></div>
            @error('phone_number')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Date of Birth -->
        <div class="col-12">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                value="{{ old('date_of_birth') }}" required>
            @error('date_of_birth')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <div class="input-group" id="show_hide_password">
                <input type="password" class="form-control border-end-0" id="password" name="password" required
                    placeholder="Enter Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
            </div>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="col-12">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group" id="show_hide_password_confirmation">
                <input type="password" class="form-control border-end-0" id="password_confirmation"
                    name="password_confirmation" required placeholder="Confirm Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
            </div>
            @error('password_confirmation')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>




        <!-- CV Upload -->
        <div class="col-12">
            <label for="cv" class="form-label">Upload CV</label>
            <input type="file" class="form-control" id="cv" name="cv" required>
            @error('cv')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <!-- Years of Experience -->
        <div class="col-12">
            <label for="years_of_experience" class="form-label">Years of Experience</label>
            <input type="number" class="form-control" id="years_of_experience" name="years_of_experience"
                value="{{ old('years_of_experience') }}" required placeholder="Number of years">
            @error('years_of_experience')
                <span class="text-danger">{{ $message }}</span>
            @enderror
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


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phone_number");
            const countryCodeInput = document.querySelector("#country_code");
            // const countrySelect = document.querySelector("#country_location");
            const countryinput = document.querySelector("#country_location-input");
            const phoneError = document.querySelector("#phone_error");

            const iti = window.intlTelInput(phoneInput, {
                initialCountry: "auto",

                geoIpLookup: function(callback) {
                    fetch('https://ipinfo.io?token=f77be74db12b48')
                        .then(response => response.json())
                        .then(data => {
                            const countryCode = (data && data.country) ? data.country : "us";
                            callback(countryCode);
                        });
                },
                excludeCountries: ["is", "Israel"],
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
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
                // countrySelect.value = countryData.name.split(" ")[0]; // Use only the first word in English
                countryinput.value = countryData.name.split(" ")[0];
                validatePhoneNumber();
            });

            // fetch("https://restcountries.com/v3.1/all")
            //     .then(response => response.json())
            //     .then(data => {
            //         data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            //         data = data.filter(country => country.name.common !== "Israel"); // Exclude Israel
            //         data.forEach(country => {
            //             const option = document.createElement("option");
            //             option.value = country.name.common;
            //             option.textContent = country.name.common;
            //             countrySelect.appendChild(option);
            //         });
            //     });

            // Validate passwords
            function validatePasswords() {
                const passwordInput = document.querySelector("#password");
                const confirmPasswordInput = document.querySelector("#password_confirmation");
                const passwordError = document.querySelector("#password_error");
                const confirmPasswordError = document.querySelector("#confirm_password_error");

                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                if (password.length < 6) {
                    passwordError.textContent = "Password must be at least 6 characters.";
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

            // Handle form submission
            document.querySelector('form').addEventListener('submit', function(event) {
                const isPhoneValid = validatePhoneNumber();
                const arePasswordsValid = validatePasswords();

                if (!isPhoneValid || !arePasswordsValid) {
                    event.preventDefault();
                    alert("Please correct the errors before submitting the form.");
                }
            });
        });
    </script>
</x-guest-layout>
