<x-guest-layout>
    <h2 class="text-2xl font-bold text-prabhu-red-600 text-center mb-6">{{ __('Create New User') }}</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- First Name -->
        <div>
            <x-input-label for="first_name" :value="__('First Name')" class="text-prabhu-dark" />
            <x-text-input id="first_name" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>

        <!-- Last Name -->
        <div class="mt-4">
            <x-input-label for="last_name" :value="__('Last Name')" class="text-prabhu-dark" />
            <x-text-input id="last_name" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-prabhu-dark" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" class="text-prabhu-dark" />
            <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm">
                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-prabhu-dark" />

            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-prabhu-dark" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-prabhu-red-600 hover:text-prabhu-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-prabhu-red-500" href="{{ route('users.index') }}">
                {{ __('Back to Users') }}
            </a>

            <x-primary-button class="ms-3 bg-prabhu-red-600 hover:bg-prabhu-red-700 focus:ring-prabhu-red-500">
                {{ __('Create User') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
