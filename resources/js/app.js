import Alpine from 'alpinejs';
import './custom-select';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        toasts: [],

        show(message, type = 'success', duration = 4000) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, visible: true });

            if (duration > 0) {
                setTimeout(() => this.remove(id), duration);
            }
        },

        success(message, duration) {
            this.show(message, 'success', duration);
        },

        error(message, duration) {
            this.show(message, 'error', duration);
        },

        warning(message, duration) {
            this.show(message, 'warning', duration);
        },

        info(message, duration) {
            this.show(message, 'info', duration);
        },

        remove(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    });

    Alpine.store('confirm', {
        show: false,
        title: 'Confirm Action',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        onConfirm: null,

        ask({ title = 'Confirm Action', message = '', confirmText = 'Confirm', cancelText = 'Cancel', onConfirm = null }) {
            this.title = title;
            this.message = message;
            this.confirmText = confirmText;
            this.cancelText = cancelText;
            this.onConfirm = onConfirm;
            this.show = true;

            Alpine.nextTick(() => document.querySelector('[data-confirm-cancel]')?.focus());
        },

        askForForm({ title = 'Confirm Action', message = '', confirmText = 'Confirm', cancelText = 'Cancel', formId = null }) {
            this.ask({
                title,
                message,
                confirmText,
                cancelText,
                onConfirm: () => {
                    const form = formId ? document.getElementById(formId) : null;
                    form?.submit();
                },
            });
        },

        close() {
            this.show = false;
            this.title = 'Confirm Action';
            this.message = '';
            this.confirmText = 'Confirm';
            this.cancelText = 'Cancel';
            this.onConfirm = null;
        },

        proceed() {
            const action = this.onConfirm;

            this.close();
            action?.();
        },
    });
});

document.addEventListener('click', (event) => {
    const tab = event.target.closest('[data-tabs] [data-tab]');

    if (!tab) {
        return;
    }

    event.preventDefault();

    const tabs = tab.closest('[data-tabs]');
    const tabName = tab.dataset.tab;
    const scope = tabs.parentElement;

    tabs.querySelectorAll('[data-tab]').forEach(item => item.classList.toggle('active', item === tab));
    scope.querySelectorAll('[data-tab-panel]').forEach(panel => {
        panel.classList.toggle('hide', panel.dataset.tabPanel !== tabName);
    });
});

Alpine.start();
