@php($branch = $branch ?? null)

<div class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <x-input-label for="fiscal_year" :value="__('Fiscal Year')" />
        <select id="fiscal_year" name="fiscal_year" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
            <option value="">Select Fiscal Year</option>
            @foreach ($fiscalYears as $fiscalYear)
                <option value="{{ $fiscalYear }}" {{ old('fiscal_year', $branch?->fiscal_year) === $fiscalYear ? 'selected' : '' }}>{{ $fiscalYear }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('fiscal_year')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="month" :value="__('Month')" />
        <select id="month" name="month" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm" required>
            <option value="">Select Month</option>
            @foreach ($monthNames as $monthValue => $monthName)
                <option value="{{ $monthValue }}" {{ (string) old('month', $branch?->month) === (string) $monthValue ? 'selected' : '' }}>{{ $monthName }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('month')" class="mt-2" />
    </div>
</div>
