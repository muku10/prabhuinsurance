<x-guest-layout>
    <h2 class="text-2xl font-bold text-prabhu-red-600 text-center mb-6">{{ __('Confirm Password') }}</h2>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-prabhu-dark" />

            <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="bg-prabhu-red-600 hover:bg-prabhu-red-700 focus:ring-prabhu-red-500">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
