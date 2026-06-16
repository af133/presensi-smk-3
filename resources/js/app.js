window.Alpine = Alpine;

Alpine.store('sidebar', {
    open: window.innerWidth > 768,
    
    toggle() {
        this.open = !this.open;
    },
    closeIfMobile() {
        if (window.innerWidth <= 768) {
            this.open = false;
        }
    }
});

Alpine.start();