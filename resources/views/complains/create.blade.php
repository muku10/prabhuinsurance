<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Complain') }}
            </h2>
            <a href="{{ route('complains.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Complains
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('complains.store') }}">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', date('Y'))" required min="2000" max="2100" />
                                <x-input-error :messages="$errors->get('year')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <x-text-input id="month" class="block mt-1 w-full" type="number" name="month" :value="old('month', date('n'))" required min="1" max="12" />
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="complain_type" :value="__('Complain Type')" />
                            <x-text-input id="complain_type" class="block mt-1 w-full" type="text" name="complain_type" :value="old('complain_type')" required autofocus />
                            <x-input-error :messages="$errors->get('complain_type')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="received_num" :value="__('Received')" />
                                <x-text-input id="received_num" class="block mt-1 w-full" type="number" name="received_num" :value="old('received_num', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('received_num')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="resolved_num" :value="__('Resolved')" />
                                <x-text-input id="resolved_num" class="block mt-1 w-full" type="number" name="resolved_num" :value="old('resolved_num', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('resolved_num')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="pending_num" :value="__('Pending')" />
                                <x-text-input id="pending_num" class="block mt-1 w-full" type="number" name="pending_num" :value="old('pending_num', 0)" required min="0" />
                                <x-input-error :messages="$errors->get('pending_num')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="average_resolution_time" :value="__('Average Resolution Time (Days)')" />
                            <x-text-input id="average_resolution_time" class="block mt-1 w-full" type="number" name="average_resolution_time" :value="old('average_resolution_time')" min="0" step="0.01" />
                            <x-input-error :messages="$errors->get('average_resolution_time')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
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