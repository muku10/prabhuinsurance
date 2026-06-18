<div
    x-data
    x-show="$store.confirm.show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="global-confirm-modal-title"
    role="dialog"
    aria-modal="true"
    x-on:keydown.escape.window="$store.confirm.close()"
>
    <div
        x-show="$store.confirm.show"
        x-transition.opacity
        class="fixed inset-0 bg-gray-900/60"
        aria-hidden="true"
    ></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
        <section
            x-show="$store.confirm.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-3 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-3 sm:scale-95"
            x-on:click.outside="$store.confirm.close()"
            class="relative w-full max-w-md overflow-hidden rounded-xl bg-white text-left shadow-2xl ring-1 ring-black/5"
        >
            <div class="border-b border-gray-100 px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 ring-8 ring-red-50/60">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900" id="global-confirm-modal-title" x-text="$store.confirm.title"></h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600" x-text="$store.confirm.message"></p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    data-confirm-cancel
                    x-on:click="$store.confirm.close()"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-prabhu-red-500 focus:ring-offset-2"
                    x-text="$store.confirm.cancelText"
                ></button>
                <button
                    type="button"
                    x-on:click="$store.confirm.proceed()"
                    class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    x-text="$store.confirm.confirmText"
                ></button>
            </div>
        </section>
    </div>
</div>