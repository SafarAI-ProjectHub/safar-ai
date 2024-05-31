<section class="space-y-6">
    <header class="mb-4">
        <h2 class="h4 font-weight-bold text-dark">
            {{ __('Delete Account') }}
        </h2>
        <p class="text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button class="btn btn-danger" x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <h2 class="h4 font-weight-bold text-dark">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="text-muted">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="form-group mt-3">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="form-control"
                    placeholder="{{ __('Password') }}">
                @error('password', 'userDeletion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-secondary me-3" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn btn-danger">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
