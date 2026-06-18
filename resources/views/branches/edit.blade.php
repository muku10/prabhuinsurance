<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Branch') }}
            </h2>
            <a href="{{ route('branches.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Branches
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <x-input-label for="province_id" :value="__('Province')" />
                            <select id="province_id" name="province_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="">Select Province</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->province_id }}" {{ old('province_id', $branch->province_id) == $province->province_id ? 'selected' : '' }}>{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="district_id" :value="__('District')" />
                            <select id="district_id" name="district_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="">Select District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->district_id }}" {{ old('district_id', $branch->district_id) == $district->district_id ? 'selected' : '' }}>{{ $district->district_name }} ({{ $district->province->province_name }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('district_id')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $branch->year)" required min="2000" max="2100" />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <x-text-input id="month" class="block mt-1 w-full" type="number" name="month" :value="old('month', $branch->month)" required min="1" max="12" />
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="number" :value="__('Number')" />
                                <x-text-input id="number" class="block mt-1 w-full" type="number" name="number" :value="old('number', $branch->number)" required min="0" />
                                <x-input-error :messages="$errors->get('number')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>{{ __('Update') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>