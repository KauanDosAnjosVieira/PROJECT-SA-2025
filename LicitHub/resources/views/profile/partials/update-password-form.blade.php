@vite(['resources/css/login.css'])
<section class="table-container mb-6">
    <header class="mb-4">
        <h2 class="text-xl font-semibold text-white">
            {{ __('Update Password') }}
        </h2>
        <p class="text-sm text-muted mt-1">
            {{ __('Certifique-se de usar uma senha longa e aleatória para manter a segurança') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block mb-1 text-sm font-medium">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password"
                   class="form-input w-full" required autocomplete="current-password" />
        </div>

        <div>
            <label for="password" class="block mb-1 text-sm font-medium">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password"
                   class="form-input w-full" required autocomplete="new-password" />
        </div>

        <div>
            <label for="password_confirmation" class="block mb-1 text-sm font-medium">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                   class="form-input w-full" required autocomplete="new-password" />
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</section>
