<x-guest-layout>
    <x-slot name="imageSlot">
        <img src="{{ asset('assets/images/login-images/login-cover.svg') }}" class="img-fluid auth-img-cover-login" width="650" alt=""/>
    </x-slot>

    <form method="POST" action="{{ route('login') }}" class="row g-3">
        @csrf

        <!-- Email Address -->
        <div class="col-12">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="john@example.com">
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <div class="input-group" id="show_hide_password">
                <input type="password" class="form-control border-end-0" id="password" name="password" required autocomplete="current-password" placeholder="Enter Password">
                <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
            </div>
            @error('password')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="col-md-6">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Remember Me</label>
            </div>
        </div>

        <!-- Forgot Password -->
        <div class="col-md-6 text-end">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="col-12">
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign in</button>
            </div>
        </div>

        <!-- Sign Up Links -->
        <div class="col-12 text-center">
            <p class="mb-0">Don't have an account yet?<br> <a href="{{ route('register') }}">Signup as Student</a> | <a href="{{ route('register-teacher') }}">Signup as Teacher</a></p>
        </div>
    </form>
</x-guest-layout>
