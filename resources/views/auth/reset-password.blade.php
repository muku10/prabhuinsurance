<x-guest-layout>
    <form class="login-form auth-card" method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-logo-mobile">
            <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance">
        </div>

        <div class="auth-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 17v-4" />
                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                <rect width="16" height="10" x="4" y="10" rx="2" />
            </svg>
        </div>

        <h1>Create new password</h1>
        <p class="sub">Choose a strong password to secure your Prabhu Insurance admin account.</p>

        <div class="field">
            <label for="email">Email address</label>
            <input
                class="input"
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                placeholder="you@prabhuinsurance.com"
                required
                autofocus
                autocomplete="username"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="field">
            <label for="password">New password</label>
            <input
                class="input"
                id="password"
                type="password"
                name="password"
                placeholder="Enter new password"
                required
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input
                class="input"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="Confirm new password"
                required
                autocomplete="new-password"
            >
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="btn btn-primary w-full auth-submit">Reset Password</button>

        <div class="login-foot">
            Remember your password?
            <a href="{{ route('login') }}">Back to sign in</a>
        </div>
    </form>
</x-guest-layout>
