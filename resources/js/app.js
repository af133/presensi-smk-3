import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.store('sidebar', {
    open: window.innerWidth > 768,
    toggle() {
        this.open = !this.open;
    }
});
Alpine.start();