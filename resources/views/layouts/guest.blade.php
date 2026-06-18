<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Prabhu Insurance') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-login-body">
        <x-toast />

        <div class="login-page">
            <aside class="login-brand">
                <div class="logo-card">
                    <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance">
                </div>

                <div>
                    <h2>Transparency Dashboard & Monthly Reporting System</h2>
                    <p>Secure portal for authorized personnel to upload monthly business data, manage master records, and publish transparent analytics to stakeholders.</p>
                </div>

                <div style="opacity:.8; font-size:12px; position:relative;">© {{ date('Y') }} Prabhu Insurance Ltd. · Powered by Codeilo Solutions</div>
            </aside>

            <main class="login-form-wrap">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
