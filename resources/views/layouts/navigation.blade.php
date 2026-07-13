<aside class="sidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Prabhu">
        <div class="name">Prabhu Insurance<small>Admin Portal</small></div>
    </div>

    <nav class="nav">
        <div class="section">Main</div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>Dashboard
        </a>
        <a href="{{ route('upload.create') }}" class="{{ request()->routeIs('upload.create') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Upload Data
        </a>
        <a href="{{ route('upload.import-module') }}" class="{{ request()->routeIs('upload.import-module') || request()->routeIs('upload.import-module.store') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg>Import Data
        </a>
        <a href="{{ route('import-logs.index') }}" class="{{ request()->routeIs('import-logs.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>Upload History
        </a>
        <a href="{{ route('upload.database-history') }}" class="{{ request()->routeIs('upload.database-history') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/><path d="M8 3v18"/></svg>Database Import History
        </a>
        <div class="nav-group" x-data="{ open: {{ request()->routeIs('financial-highlights.*') ? 'true' : 'false' }} }">
            <button
                type="button"
                class="nav-parent {{ request()->routeIs('financial-highlights.*') ? 'active' : '' }}"
                x-on:click="open = !open"
                x-bind:aria-expanded="open.toString()"
                aria-controls="financial-highlights-menu"
            >
                <span class="nav-parent-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-7"/></svg>
                </span>
                <span class="nav-parent-copy"><strong>Financial Highlights</strong><small>Quarterly reporting</small></span>
                <svg class="nav-chevron" x-bind:class="{ 'open': open }" viewBox="0 0 24 24" fill="none" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
            <div
                id="financial-highlights-menu"
                class="nav-children"
                x-show="open"
                x-cloak
                x-transition:enter="nav-submenu-enter"
                x-transition:enter-start="nav-submenu-enter-start"
                x-transition:enter-end="nav-submenu-enter-end"
                x-transition:leave="nav-submenu-leave"
                x-transition:leave-start="nav-submenu-enter-end"
                x-transition:leave-end="nav-submenu-enter-start"
            >
                <a href="{{ route('financial-highlights.upload') }}" class="{{ request()->routeIs('financial-highlights.upload') || request()->routeIs('financial-highlights.store') || request()->routeIs('financial-highlights.template') ? 'active' : '' }}">
                    <span class="nav-child-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4"/><path d="m7 9 5-5 5 5"/><path d="M4 20h16"/></svg></span>
                    <span>Upload</span>
                </a>
                <a href="{{ route('financial-highlights.import') }}" class="{{ request()->routeIs('financial-highlights.import') || request()->routeIs('financial-highlights.import.store') || request()->routeIs('financial-highlights.history') ? 'active' : '' }}">
                    <span class="nav-child-icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/></svg></span>
                    <span>Import &amp; History</span>
                </a>
            </div>
        </div>
        <a href="{{ route('master-data.index') }}" class="{{ request()->routeIs('master-data.*') || request()->routeIs('provinces.*') || request()->routeIs('districts.*') || request()->routeIs('policies.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v6c0 1.66 4 3 9 3s9-1.34 9-3V5"/><path d="M3 11v6c0 1.66 4 3 9 3s9-1.34 9-3v-6"/></svg>Master Data
        </a>

        <div class="section">Reports</div>
        <a href="{{ route('public.view') }}" target="_blank" rel="noopener">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>Public View
        </a>

        <div class="section">Account</div>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>Users
        </a>
        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to logout?')">
            @csrf
            <button type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout
            </button>
        </form>
    </nav>
</aside>
