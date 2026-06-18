<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Policy Type') }}
            </h2>
            <a href="{{ route('policies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Policy Types
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('policies.update', $policy->policy_id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <x-input-label for="parent_id" :value="__('Parent Policy (optional)')" />
                            <select id="parent_id" name="parent_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm">
                                <option value="">None</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->policy_id }}" {{ old('parent_id', $policy->parent_id) == $parent->policy_id ? 'selected' : '' }}>{{ $parent->policy_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="policy_name" :value="__('Policy Name')" />
                            <x-text-input id="policy_name" class="block mt-1 w-full" type="text" name="policy_name" :value="old('policy_name', $policy->policy_name)" required autofocus />
                            <x-input-error :messages="$errors->get('policy_name')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="policy_name_np" :value="__('Nepali Name')" />
                            <x-text-input id="policy_name_np" class="block mt-1 w-full" type="text" name="policy_name_np" :value="old('policy_name_np', $policy->policy_name_np)" />
                            <x-input-error :messages="$errors->get('policy_name_np')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status', $policy->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $policy->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
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