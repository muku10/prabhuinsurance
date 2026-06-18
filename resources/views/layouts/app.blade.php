<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Prabhu Insurance') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data class="admin-body">
    <div class="app">
        @include('layouts.navigation')

        <div class="main">
            <header class="topbar">
                <div>
                    <div class="crumbs">Home · {{ $crumbs ?? trim(strip_tags($header ?? 'Dashboard')) }}</div>
                    <h1>{{ $title ?? trim(strip_tags($header ?? 'Dashboard')) }}</h1>
                </div>
                <div class="user">
                    <span style="font-size:13px;color:var(--muted)">{{ Auth::user()->first_name ?? 'Admin' }} {{ Auth::user()->last_name ?? 'User' }}</span>
                    <div class="avatar">{{ strtoupper(substr(Auth::user()->first_name ?? 'A', 0, 1) . substr(Auth::user()->last_name ?? 'U', 0, 1)) }}</div>
                </div>
            </header>

            <x-toast />
            <x-confirm-modal />

            <main class="content">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
