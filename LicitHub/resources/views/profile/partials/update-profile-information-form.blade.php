@vite(['resources/css/login.css'])
<section class="table-container mb-6">
    <header class="mb-4">
        <h2 class="text-xl font-semibold text-white">
            {{ __('Profile Information') }}
        </h2>
        <p class="text-sm text-muted mt-1">
            {{ __('Atualize suas informações de perfil e endereço de e-mail') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block mb-1 text-sm font-medium">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                   class="form-input w-full" required autocomplete="name" />
        </div>

        <div>
            <label for="email" class="block mb-1 text-sm font-medium">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                   class="form-input w-full" required autocomplete="email" />
        </div>

        <div class="form-actions">
            <button class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</section>
