<div
    x-data
    x-on:toast-show.window="$store.toast.show($event.detail.message, $event.detail.type, $event.detail.duration)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none"
>
    <template x-for="toast in $store.toast.toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="pointer-events-auto w-full rounded-lg shadow-lg border-l-4 overflow-hidden"
            :class="{
                'bg-green-50 border-green-500': toast.type === 'success',
                'bg-red-50 border-red-500': toast.type === 'error',
                'bg-yellow-50 border-yellow-500': toast.type === 'warning',
                'bg-blue-50 border-blue-500': toast.type === 'info'
            }"
        >
            <div class="flex items-start p-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <svg x-show="toast.type === 'success'" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="toast.type === 'error'" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="toast.type === 'warning'" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="toast.type === 'info'" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <!-- Message -->
                <div class="ml-3 flex-1">
                    <p
                        class="text-sm font-medium"
                        :class="{
                            'text-green-800': toast.type === 'success',
                            'text-red-800': toast.type === 'error',
                            'text-yellow-800': toast.type === 'warning',
                            'text-blue-800': toast.type === 'info'
                        }"
                        x-text="toast.message"
                    ></p>
                </div>

                <!-- Close -->
                <div class="ml-4 flex-shrink-0 flex">
                    <button
                        @click="$store.toast.remove(toast.id)"
                        class="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

@if (session('toast'))
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                const toast = Alpine.store('toast');
                if (toast) {
                    toast.show(
                        @json(session('toast')['message']),
                        @json(session('toast')['type'] ?? 'success'),
                        @json(session('toast')['duration'] ?? 4000)
                    );
                }
            }, 100);
        });
    </script>
@endif