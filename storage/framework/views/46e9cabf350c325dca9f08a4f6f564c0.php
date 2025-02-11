<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10">
<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\GuestLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('imageSlot', null, []); ?> 
        <img src="<?php echo e(asset('assets/images/login-images/register-cover.svg')); ?>" class="img-fluid auth-img-cover-login"
            width="650" alt="" />
     <?php $__env->endSlot(); ?>

    <form id="registerForm" class="row g-3">
        <?php echo csrf_field(); ?>

        <!-- First Name -->
        <div class="col-12">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo e(old('first_name')); ?>"
                required placeholder="John">
            <span class="text-danger d-block" id="error_first_name"></span>
        </div>

        <!-- Last Name -->
        <div class="col-12">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo e(old('last_name')); ?>"
                required placeholder="Doe">
            <span class="text-danger d-block" id="error_last_name"></span>
        </div>

        <!-- Email Address -->
        <div class="col-12">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo e(old('email')); ?>"
                required placeholder="example@user.com">
            <span class="text-danger d-block" id="error_email"></span>
        </div>

        <!-- Country -->
        <div class="col-12">
            <input type="hidden" name="country_location" id="country_location-input" value="">
            <span class="text-danger d-block" id="error_country_location"></span>
        </div>

        <!-- Phone Number -->
        <div class="col-12">
            <label for="phone_number" class="form-label">Phone Number</label>
            <div class="input-group">
                <input type="text" class="form-control" id="country_code" name="country_code" readonly>
                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                    value="<?php echo e(old('phone_number')); ?>" required placeholder="1234567890">
            </div>
            <div id="phone_error" class="text-danger"></div>
            <span class="text-danger d-block" id="error_phone_number"></span>
        </div>

        <!-- Date of Birth -->
        <div class="col-12">
            <label for="date_of_birth" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                value="<?php echo e(old('date_of_birth')); ?>" required>
            <span class="text-danger d-block" id="error_date_of_birth"></span>
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <div class="input-group" id="show_hide_password">
                <input type="password" class="form-control border-end-0" id="password" name="password" required
                    placeholder="Enter Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
            </div>
            <span class="text-danger d-block" id="error_password"></span>
        </div>

        <!-- Confirm Password -->
        <div class="col-12">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group" id="show_hide_password_confirmation">
                <input type="password" class="form-control border-end-0" id="password_confirmation"
                    name="password_confirmation" required placeholder="Confirm Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
            </div>
            <span class="text-danger d-block" id="error_password_confirmation"></span>
        </div>

        <!-- Terms and Conditions -->
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="terms" required>
                <label class="form-check-label" for="flexSwitchCheckChecked">I read and agree to <a
                        href="<?php echo e(route('terms')); ?>"> Terms &
                        Conditions </a> And <a href="<?php echo e(route('privacy')); ?>"> Privacy Policy </a></label>
            </div>
            <span class="text-danger d-block" id="error_terms"></span>
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign up</button>
            </div>
        </div>

        <!-- Sign In Link -->
        <div class="col-12 text-center">
            <p class="mb-0">Already have an account? <a href="<?php echo e(route('login')); ?>">Sign in here</a></p>
        </div>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phone_number");
            const countryCodeInput = document.querySelector("#country_code");
            const countryinput = document.querySelector("#country_location-input");
            const phoneError = document.querySelector("#phone_error");

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
                countryinput.value = countryName;
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

                fetch("<?php echo e(route('register')); ?>", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                            "Accept": "application/json"
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Clear previous errors
                        document.querySelectorAll('.text-danger.d-block').forEach(el => el.textContent =
                            '');

                        if (data.errors) {
                            for (const [key, messages] of Object.entries(data.errors)) {
                                document.querySelector(`#error_${key}`).textContent = messages.join(
                                    ", ");
                            }
                        } else {
                            // Handle successful registration
                            window.location.href = data.redirect;
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/auth/register.blade.php ENDPATH**/ ?>