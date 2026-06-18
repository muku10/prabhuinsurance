<x-guest-layout>
    <form class="login-form auth-card" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-logo-mobile">
            <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance">
        </div>

        <div class="auth-card-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16v12H4z" />
                <path d="m4 7 8 6 8-6" />
            </svg>
        </div>

        <h1>Reset your password</h1>
        <p class="sub">Enter your registered email address and we’ll send you a secure password reset link.</p>
        <p class="auth-note">The link will expire after 5 minutes for security.</p>

        <x-auth-session-status class="auth-status" :status="session('status')" />

        <div class="field">
            <label for="email">Email address</label>
            <input
                class="input"
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@prabhuinsurance.com"
                required
                autofocus
                autocomplete="username"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="btn btn-primary w-full auth-submit">Send Reset Link</button>

        <div class="login-foot">
            Remember your password?
            <a href="{{ route('login') }}">Back to sign in</a>
        </div>
    </form>
</x-guest-layout>
