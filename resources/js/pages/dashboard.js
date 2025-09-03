export default new (class DashboardPage {
    constructor() {
        this.pageName = "Dashboard";
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        // Tambahkan inisialisasi fitur dashboard jika ada
    }

    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
        // Tambahkan logika jika halaman show dashboard perlu fitur khusus
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        // Tambahkan logika pembuatan item baru, jika diperlukan
    }

    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        // Tambahkan logika pengeditan data dashboard
    }
})();