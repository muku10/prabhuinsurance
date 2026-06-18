<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Import Log') }}
            </h2>
            <a href="{{ route('import-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Import Logs
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-t-4 border-prabhu-red-600"></div>
                <div class="p-6">
                    <form method="POST" action="{{ route('import-logs.update', $importLog->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', $importLog->date->format('Y-m-d'))" required autofocus />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-input-label :value="__('Current File')" />
                            <a href="{{ asset('storage/'.$importLog->file_name) }}" target="_blank" class="mt-1 inline-flex text-sm font-medium text-prabhu-red-600 hover:text-prabhu-red-900">
                                {{ basename($importLog->file_name) }}
                            </a>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="file" :value="__('Replace File')" />
                            <input id="file" class="block mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-prabhu-red-500 focus:ring-prabhu-red-500" type="file" name="file" accept=".xlsx,.xls,.csv" />
                            <p class="mt-1 text-sm text-gray-500">Leave this empty to keep the current file.</p>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="fiscal_year" :value="__('Fiscal Year')" />
                                <x-text-input id="fiscal_year" class="block mt-1 w-full" type="text" name="fiscal_year" :value="old('fiscal_year', $importLog->fiscal_year)" required />
                                <x-input-error :messages="$errors->get('fiscal_year')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="month" :value="__('Month')" />
                                <x-text-input id="month" class="block mt-1 w-full" type="number" name="month" :value="old('month', $importLog->month)" required min="1" max="12" />
                                <x-input-error :messages="$errors->get('month')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
                                <option value="pending" {{ old('status', $importLog->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ old('status', $importLog->status) === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ old('status', $importLog->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ old('status', $importLog->status) === 'failed' ? 'selected' : '' }}>Failed</option>
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