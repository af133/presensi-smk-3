import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.store('sidebar', {
    open: false,
    toggle() {
        this.open = !this.open;
    }
});
Alpine.start();