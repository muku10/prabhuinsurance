<x-guest-layout>
    <form class="login-form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-logo-mobile">
            <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance">
        </div>

        <h1>Welcome back</h1>
        <p class="sub">Sign in to manage transparency reports.</p>

        <x-auth-session-status class="mb-4 text-sm text-green-700" :status="session('status')" />

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

        <div class="field">
            <label for="password">Password</label>
            <input
                class="input"
                id="password"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="actions">
            <label class="check" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size:13px; font-weight:600;">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">Sign In</button>

        <div class="login-foot">Protected portal. Unauthorized access is prohibited.</div>
    </form>
</x-guest-layout>
