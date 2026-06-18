<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add District') }}
            </h2>
            <a href="{{ route('districts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Districts
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('districts.store') }}">
                        @csrf
                        <div class="mb-4">
                            <x-input-label for="province_id" :value="__('Province')" />
                            <select id="province_id" name="province_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="">Select Province</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->province_id }}" {{ old('province_id') == $province->province_id ? 'selected' : '' }}>{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="district_name" :value="__('District Name')" />
                            <x-text-input id="district_name" class="block mt-1 w-full" type="text" name="district_name" :value="old('district_name')" required autofocus />
                            <x-input-error :messages="$errors->get('district_name')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>