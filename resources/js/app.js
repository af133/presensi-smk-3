import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('sidebar', {
        open: window.innerWidth > 768 
            ? (localStorage.getItem('sidebar_open') === 'true' || localStorage.getItem('sidebar_open') === null)
            : false,

        toggle() {
            this.open = !this.open;
            localStorage.setItem('sidebar_open', this.open);
        }
    });
});

Alpine.start();