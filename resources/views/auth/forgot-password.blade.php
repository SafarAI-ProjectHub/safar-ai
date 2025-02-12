<x-guest-layout>
    <x-slot name="imageSlot">
        <img src="{{ asset('assets/images/login-images/forgot-password-cover.svg') }}" class="img-fluid" width="600"
            alt="" />
    </x-slot>

    <div class="p-3">
        @if (session('alert-message'))
            <x-alert :type="session('alert-type', 'info')" :message="session('alert-message')" :icon="session('alert-icon')" />
        @endif
        {{-- <div class="text-center">
            <img src="{{ asset('assets/images/icons/forgot-2.png') }}" width="100" alt="" />
        </div> --}}
        <h4 class="mt-5 font-weight-bold">Forgot Password?</h4>
        <p class="text-muted">Enter your registered email ID to reset the password</p>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="my-4">
                <label class="form-label">Email ID</label>
                <input type="email" class="form-control" name="email" placeholder="example@user.com" required />
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Send</button>
                <a href="{{ route('login') }}" class="btn btn-light">
                    <i class='bx bx-arrow-back me-1'></i>Back to Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
