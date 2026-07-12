@php($branch = $branch ?? null)

<div id="inactive_period_fields" class="grid grid-cols-2 gap-4 mb-4">
    <div>
        <x-input-label for="inactive_fiscal_year" :value="__('Inactive Fiscal Year')" />
        <select id="inactive_fiscal_year" name="inactive_fiscal_year" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm">
            <option value="">Select Fiscal Year</option>
            @foreach ($fiscalYears as $fiscalYear)
                <option value="{{ $fiscalYear }}" {{ old('inactive_fiscal_year', $branch?->inactive_fiscal_year) === $fiscalYear ? 'selected' : '' }}>{{ $fiscalYear }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('inactive_fiscal_year')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="inactive_month" :value="__('Inactive Month')" />
        <select id="inactive_month" name="inactive_month" class="mt-1 block w-full border-gray-300 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm">
            <option value="">Select Month</option>
            @foreach ($monthNames as $monthValue => $monthName)
                <option value="{{ $monthValue }}" {{ (string) old('inactive_month', $branch?->inactive_month) === (string) $monthValue ? 'selected' : '' }}>{{ $monthName }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('inactive_month')" class="mt-2" />
    </div>
</div>
