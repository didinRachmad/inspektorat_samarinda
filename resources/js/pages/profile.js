export default new (class ProfilePage {
    constructor() {
        this.pageName = "Profile";
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
    }

    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
    }

    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
    }
})();
