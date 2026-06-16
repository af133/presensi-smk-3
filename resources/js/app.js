window.Alpine = Alpine;

Alpine.store('sidebar', {
    // 1. Cek apakah ada status tersimpan di browser
    // Jika tidak ada, baru pakai logika lebar layar (default terbuka di desktop)
    open: localStorage.getItem('sidebar_open') !== null 
        ? localStorage.getItem('sidebar_open') === 'true' 
        : window.innerWidth > 768,

    toggle() {
        this.open = !this.open;
        // 2. Simpan setiap ada perubahan
        localStorage.setItem('sidebar_open', this.open);
    },

    closeIfMobile() {
        if (window.innerWidth <= 768) {
            this.open = false;
            localStorage.setItem('sidebar_open', false);
        }
    }
});

Alpine.start();