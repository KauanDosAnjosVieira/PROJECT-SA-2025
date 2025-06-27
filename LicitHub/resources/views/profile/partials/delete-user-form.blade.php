@vite(['resources/css/login.css'])


<section class="table-container mb-6">
    <header class="mb-4">
        <h2 class="text-xl font-semibold text-white">
            {{ __('Delete Account') }}
        </h2>
        <p class="text-sm text-muted mt-1">
            {{ __('Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
        @csrf
        @method('delete')

        <div>
            <label for="password" class="block mb-1 text-sm font-medium">{{ __('Password') }}</label>
            <input id="password" name="password" type="password"
                   class="form-input w-full" required autocomplete="current-password" />
        </div>

        <div class="form-actions">
            <button class="btn btn-danger">
                {{ __('Delete Account') }}
            </button>
        </div>
    </form>
</section>
