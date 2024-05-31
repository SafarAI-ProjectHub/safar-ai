<x-guest-layout>
    <x-slot name="imageSlot">
        <img src="{{ asset('assets/images/login-images/reset-password-cover.svg') }}" class="img-fluid" width="600"
            alt="" />
    </x-slot>

    <div class="">
        {{-- <div class="mb-4 text-center">
            <img src="{{ asset('assets/images/logo-icon.png') }}" width="60" alt="" />
        </div> --}}
        <div class="text-start mb-4">
            <h5 class="">Generate New Password</h5>
            <p class="mb-0">We received your reset password request. Please enter your new password!</p>
        </div>
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3 mt-4">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required
                    autofocus>
            </div>
            <div class="mb-3 mt-4">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="password" required placeholder="Enter new password" />
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" required
                    placeholder="Confirm password" />
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="{{ route('login') }}" class="btn btn-light">
                    <i class='bx bx-arrow-back mr-1'></i>Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
