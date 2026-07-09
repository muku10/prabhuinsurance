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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('branches.update', $branch->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="branch_code" :value="__('Branch Code')" />
                                <x-text-input id="branch_code" class="block mt-1 w-full" type="number" name="branch_code" :value="old('branch_code', $branch->branch_code)" required min="1" />
                                <x-input-error :messages="$errors->get('branch_code')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="ext_branch_code" :value="__('Ext Branch Code')" />
                                <x-text-input id="ext_branch_code" class="block mt-1 w-full" type="text" name="ext_branch_code" :value="old('ext_branch_code', $branch->ext_branch_code)" required maxlength="10" />
                                <x-input-error :messages="$errors->get('ext_branch_code')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="branch_name" :value="__('Branch Name')" />
                                <x-text-input id="branch_name" class="block mt-1 w-full" type="text" name="branch_name" :value="old('branch_name', $branch->branch_name)" required />
                                <x-input-error :messages="$errors->get('branch_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="province_id" :value="__('Province')" />
                                <select id="province_id" name="province_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                    <option value="">Select Province</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->province_id }}" {{ old('province_id', $branch->province_id) == $province->province_id ? 'selected' : '' }}>{{ $province->province_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="district_id" :value="__('District')" />
                                <select id="district_id" name="district_id" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required disabled>
                                    <option value="">Select Province First</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->district_id }}" data-province-id="{{ $district->province_id }}" {{ old('district_id', $branch->district_id) == $district->district_id ? 'selected' : '' }}>{{ $district->district_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('district_id')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <x-input-label for="local_level" :value="__('Local Level')" />
                                <x-text-input id="local_level" class="block mt-1 w-full" type="number" name="local_level" :value="old('local_level', $branch->local_level)" min="1" />
                                <x-input-error :messages="$errors->get('local_level')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $branch->address)" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="display_name" :value="__('Display Name')" />
                                <x-text-input id="display_name" class="block mt-1 w-full" type="text" name="display_name" :value="old('display_name', $branch->display_name)" />
                                <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const provinceSelect = document.getElementById('province_id');
            const districtSelect = document.getElementById('district_id');
            const districtOptions = Array.from(districtSelect.options).slice(1);

            function filterDistricts(shouldReset = false) {
                const provinceId = provinceSelect.value;

                districtSelect.disabled = !provinceId;
                districtSelect.options[0].textContent = provinceId ? 'Select District' : 'Select Province First';

                districtOptions.forEach((option) => {
                    option.hidden = option.dataset.provinceId !== provinceId;
                });

                if (shouldReset || !provinceId || districtSelect.selectedOptions[0]?.dataset.provinceId !== provinceId) {
                    districtSelect.value = '';
                }
            }

            provinceSelect.addEventListener('change', () => filterDistricts(true));
            filterDistricts(false);
        });
    </script>
</x-app-layout>
