<script>
    document.addEventListener('DOMContentLoaded', () => {
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const statusSelect = document.getElementById('status');
        const inactivePeriodFields = document.getElementById('inactive_period_fields');
        const inactiveFiscalYear = document.getElementById('inactive_fiscal_year');
        const inactiveMonth = document.getElementById('inactive_month');
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

        function toggleInactivePeriodFields() {
            const isInactive = statusSelect.value === 'inactive';

            inactivePeriodFields.hidden = !isInactive;
            inactiveFiscalYear.required = isInactive;
            inactiveMonth.required = isInactive;

            if (!isInactive) {
                inactiveFiscalYear.value = '';
                inactiveMonth.value = '';
            }
        }

        provinceSelect.addEventListener('change', () => filterDistricts(true));
        statusSelect.addEventListener('change', toggleInactivePeriodFields);

        filterDistricts(false);
        toggleInactivePeriodFields();
    });
</script>
