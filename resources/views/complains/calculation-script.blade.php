<script>
    document.addEventListener('DOMContentLoaded', () => {
        const received = document.getElementById('received_num');
        const resolved = document.getElementById('resolved_num');
        const pending = document.getElementById('pending_num');
        const rate = document.getElementById('resolution_rate');

        const calculate = () => {
            const receivedValue = Math.max(0, Number(received.value) || 0);
            const resolvedValue = Math.max(0, Number(resolved.value) || 0);
            pending.value = Math.max(0, receivedValue - resolvedValue);
            rate.value = `${receivedValue > 0 ? ((resolvedValue / receivedValue) * 100).toFixed(2) : '0.00'}%`;
        };

        received.addEventListener('input', calculate);
        resolved.addEventListener('input', calculate);
        calculate();
    });
</script>
