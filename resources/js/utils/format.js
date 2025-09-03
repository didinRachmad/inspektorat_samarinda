export function formatNumber(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

export function formatRupiah(angka) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(angka);
}