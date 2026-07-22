const instances = new WeakMap();

class CustomSelect {
    constructor(select) {
        this.select = select;
        this.activeIndex = -1;
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'custom-select';
        select.parentNode.insertBefore(this.wrapper, select);
        this.wrapper.appendChild(select);
        select.classList.add('custom-select-native');
        this.trigger = document.createElement('button');
        this.trigger.type = 'button';
        this.trigger.className = 'custom-select-trigger';
        this.trigger.setAttribute('aria-haspopup', 'listbox');
        this.trigger.setAttribute('aria-expanded', 'false');
        this.trigger.innerHTML = '<span class="custom-select-value"></span><span class="custom-select-chevron" aria-hidden="true"></span>';
        this.value = this.trigger.querySelector('.custom-select-value');
        this.menu = document.createElement('div');
        this.menu.className = 'custom-select-menu';
        this.menu.setAttribute('role', 'listbox');
        this.wrapper.append(this.trigger, this.menu);
        this.bind();
        this.refresh();
    }

    bind() {
        this.trigger.addEventListener('click', () => this.toggle());
        this.trigger.addEventListener('keydown', event => this.onKeydown(event));
        this.select.addEventListener('change', () => this.refresh());
        this.select.addEventListener('focus', () => this.trigger.focus());
        this.select.addEventListener('invalid', () => this.trigger.focus());
        this.observer = new MutationObserver(() => this.refresh());
        this.observer.observe(this.select, { childList: true, subtree: true, attributes: true, attributeFilter: ['disabled', 'selected', 'style', 'class'] });
    }

    availableOptions() {
        return [...this.select.options].filter(option => {
            const hiddenGroup = option.parentElement instanceof HTMLOptGroupElement && option.parentElement.style.display === 'none';
            return !option.hidden && option.style.display !== 'none' && !hiddenGroup;
        });
    }

    refresh() {
        const selected = this.select.selectedOptions[0];
        this.value.textContent = selected?.textContent?.trim() || 'Select';
        this.trigger.disabled = this.select.disabled;
        this.trigger.setAttribute('aria-disabled', String(this.select.disabled));
        this.menu.innerHTML = '';
        this.availableOptions().forEach((option, index) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'custom-select-option';
            item.textContent = option.textContent.trim();
            item.disabled = option.disabled;
            item.setAttribute('role', 'option');
            item.setAttribute('aria-selected', String(option.selected));
            item.classList.toggle('is-selected', option.selected);
            item.addEventListener('click', () => this.choose(option.value));
            item.addEventListener('mousemove', () => { this.activeIndex = index; });
            this.menu.appendChild(item);
        });
        this.activeIndex = Math.max(0, this.availableOptions().findIndex(option => option.selected));
    }

    choose(value) {
        if (this.select.disabled) return;
        this.select.value = value;
        this.select.dispatchEvent(new Event('input', { bubbles: true }));
        this.select.dispatchEvent(new Event('change', { bubbles: true }));
        this.close();
        this.trigger.focus();
    }

    toggle() {
        if (!this.select.disabled) this.wrapper.classList.contains('is-open') ? this.close() : this.open();
    }

    open() {
        document.querySelectorAll('.custom-select.is-open').forEach(wrapper => {
            if (wrapper !== this.wrapper) instances.get(wrapper.querySelector('select'))?.close();
        });
        this.refresh();
        this.wrapper.classList.add('is-open');
        this.trigger.setAttribute('aria-expanded', 'true');
        this.menu.querySelector('.is-selected')?.scrollIntoView({ block: 'nearest' });
    }

    close() {
        this.wrapper.classList.remove('is-open');
        this.trigger.setAttribute('aria-expanded', 'false');
    }

    onKeydown(event) {
        const options = [...this.menu.querySelectorAll('.custom-select-option:not(:disabled)')];
        if (!options.length) return;
        if (event.key === 'Escape') return this.close();
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (!this.wrapper.classList.contains('is-open')) return this.open();
            return options[this.activeIndex]?.click();
        }
        if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
        event.preventDefault();
        if (!this.wrapper.classList.contains('is-open')) this.open();
        this.activeIndex = Math.min(options.length - 1, Math.max(0, this.activeIndex + (event.key === 'ArrowDown' ? 1 : -1)));
        options.forEach((option, index) => option.classList.toggle('is-active', index === this.activeIndex));
        options[this.activeIndex]?.scrollIntoView({ block: 'nearest' });
    }
}

function enhance(root = document) {
    const selects = root.matches?.('select:not([multiple]):not([data-native-select])') ? [root] : root.querySelectorAll('select:not([multiple]):not([data-native-select])');
    selects.forEach(select => {
        if (instances.has(select)) return;
        const instance = new CustomSelect(select);
        instances.set(select, instance);
    });
}

function refreshAll() {
    document.querySelectorAll('.custom-select-native').forEach(select => instances.get(select)?.refresh());
}

document.addEventListener('click', event => {
    if (event.target.closest('.custom-select')) return;
    document.querySelectorAll('.custom-select.is-open').forEach(wrapper => instances.get(wrapper.querySelector('select'))?.close());
});

document.addEventListener('DOMContentLoaded', () => {
    enhance();
    new MutationObserver(mutations => mutations.forEach(mutation => mutation.addedNodes.forEach(node => {
        if (node instanceof Element) enhance(node);
    }))).observe(document.body, { childList: true, subtree: true });
});

window.CustomSelects = { refresh: refreshAll };
