window.Alpine = Alpine;

Alpine.store('sidebar', {
    open: localStorage.getItem('sidebar_open') !== null 
        ? localStorage.getItem('sidebar_open') === 'true' 
        : window.innerWidth > 768,

    toggle() {
        this.open = !this.open;
        localStorage.setItem('sidebar_open', this.open);
    },

    updateScreenSize() {
        if (window.innerWidth <= 768) {
            this.open = false;
        } else {
            this.open = true;
        }
        localStorage.setItem('sidebar_open', this.open);
    }
});

window.addEventListener('resize', () => {
    Alpine.store('sidebar').updateScreenSize();
});

Alpine.start();