import route from "@/routes";
import { formatNumber, formatRupiah } from "@/utils/format";

const routes = {
    datatable: () => route("transaksi_delivery_orders.data"),
    getSalesOrders: () => route("transaksi_sales_orders.getSalesOrders"),
    getArea: () => route("biteship.areas"),
    cekOngkir: () => route("biteship.cek-ongkir"),
};

class DeliveryOrdersPage {
    constructor() {
        this.pageName = "Delivery Orders";
        // — jQuery
        this.datatableEl = $("#datatables");
        this.soSelectEl = $("#selectSalesOrder");
        this.areaSelectEls = $("#origin, #destination");

        // — Native
        this.detailWrapper = document.querySelector("#so-details-wrapper");
        this.btnCekOngkir = document.querySelector("#btnCekOngkir");
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initForm();
    }
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initForm();
    }
    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
    }

    // ————— INIT DataTable —————
    initDataTable() {
        this.datatableEl.DataTable({
            processing: true,
            serverSide: true,
            ajax: routes.datatable(),
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                },
                { data: "id", name: "id", visible: false },
                {
                    data: "no_do",
                    name: "no_do",
                    className: "text-center",
                },
                {
                    data: "no_so",
                    name: "no_so",
                    className: "text-center",
                },
                {
                    data: "tanggal",
                    name: "tanggal",
                    className: "text-center",
                },
                {
                    data: "customer",
                    name: "customer",
                },
                {
                    data: "metode_pembayaran",
                    name: "metode_pembayaran",
                    className: "text-center",
                },
                {
                    data: "total_qty",
                    name: "total_qty",
                    className: "text-end",
                },
                {
                    data: "total_diskon",
                    name: "total_diskon",
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", ",", 0, "Rp "),
                },
                {
                    data: "grand_total",
                    name: "grand_total",
                    className: "text-end fw-bold",
                    render: $.fn.dataTable.render.number(".", ",", 0, "Rp "),
                },
                {
                    data: "approval_level",
                    name: "approval_level",
                },
                {
                    data: "status",
                    name: "status",
                },
                {
                    data: "keterangan",
                    name: "keterangan",
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: function (data, type, row) {
                        let buttons = "";

                        // Tombol Detail
                        if (row.can_show) {
                            buttons += `
                            <a href="${row.show_url}" class="btn btn-sm rounded-4 btn-info" data-bs-toggle="tooltip" title="Detail">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        `;
                        }

                        if (row.can_approve) {
                            if (row.approval_level == 0) {
                                buttons += `
                                    <form action="${
                                        row.approve_url
                                    }" method="POST" class="d-inline form-approval">
                                        <input type="hidden" name="_token" value="${$(
                                            'meta[name="csrf-token"]'
                                        ).attr("content")}">
                                        <button type="submit" class="btn btn-sm rounded-4 btn-success btn-approve" data-bs-toggle="tooltip" title="Ajukan">
                                            <i class="bi bi-check2-square"></i>
                                        </button>
                                    </form>
                                `;
                            } else {
                                buttons += `
                                    <div class="dropdown dropstart d-inline">
                                        <button class="btn btn-sm rounded-4 btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Action">
                                            <i class="bi bi-gear-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <form action="${
                                                    row.revisi_url
                                                }" method="POST" class="form-revisi">
                                                    <input type="hidden" name="_token" value="${$(
                                                        'meta[name="csrf-token"]'
                                                    ).attr("content")}">
                                                    <button type="submit" class="dropdown-item text-warning btn-revisi">
                                                        <i class="bi bi-arrow-clockwise"></i> Revisi
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="${
                                                    row.approve_url
                                                }" method="POST" class="form-approval">
                                                    <input type="hidden" name="_token" value="${$(
                                                        'meta[name="csrf-token"]'
                                                    ).attr("content")}">
                                                    <button type="submit" class="dropdown-item text-success btn-approve">
                                                        <i class="bi bi-check2-square"></i> Approve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="${
                                                    row.reject_url
                                                }" method="POST" class="form-reject">
                                                    <input type="hidden" name="_token" value="${$(
                                                        'meta[name="csrf-token"]'
                                                    ).attr("content")}">
                                                    <button type="button" class="dropdown-item text-danger btn-reject">
                                                        <i class="bi bi-x-square-fill"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                `;
                            }
                        }

                        if (row.can_modify) {
                            if (row.can_edit) {
                                buttons += `
                                    <a href="${row.edit_url}" class="btn btn-sm rounded-4 btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                `;
                            }

                            if (row.can_delete) {
                                buttons += `
                                    <form action="${
                                        row.delete_url
                                    }" method="POST" class="d-inline form-delete">
                                        <input type="hidden" name="_token" value="${$(
                                            'meta[name="csrf-token"]'
                                        ).attr("content")}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="btn btn-sm rounded-4 btn-danger btn-delete" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                `;
                            }
                        }

                        return `<div class="d-flex justify-content-center gap-1 flex-wrap">${buttons}</div>`;
                    },
                },
            ],
            dom:
                "<'row'<'col-sm-12 col-md-3'l><'col-sm-12 col-md-6 mb-3 mb-md-0 d-flex justify-content-center align-items-center'><'col-sm-12 col-md-3 text-right'f>>" +
                "<'row py-2'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            paging: true,
            responsive: true,
            pageLength: 20,
            lengthMenu: [
                [20, 50, -1],
                [20, 50, "Semua"],
            ],
            order: [[1, "desc"]],
            columnDefs: [
                {
                    targets: 0, // Menargetkan kolom pertama
                    className: "text-center", // Menambahkan kelas text-center untuk meratakan teks ke tengah
                },
            ],
            info: true,
            language: {
                sEmptyTable: "Tidak ada data yang tersedia di tabel",
                sInfo: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                sInfoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
                sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                sInfoPostFix: "",
                sInfoThousands: ".",
                sLengthMenu: "Tampilkan _MENU_ entri",
                sLoadingRecords: "Memuat...",
                sProcessing: "Sedang memproses...",
                sSearch: "Cari:",
                sZeroRecords: "Tidak ditemukan data yang cocok",
                oAria: {
                    sSortAscending:
                        ": aktifkan untuk mengurutkan kolom secara menaik",
                    sSortDescending:
                        ": aktifkan untuk mengurutkan kolom secara menurun",
                },
            },
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                });
            },
        });
    }

    // ————— Form SO & Ongkir —————
    initForm() {
        this.setupSoSelect();
        this.setupAreaSelect();
        this.btnCekOngkir.addEventListener("click", () => this.checkOngkir());

        const shippingEl = document.getElementById("shipping-init-data");
        if (shippingEl) {
            try {
                const pricing = JSON.parse(shippingEl.dataset.shippings);
                this.renderOngkirTable(pricing);
                this.saveOngkirDataToForm(pricing);
            } catch (e) {
                console.error("Gagal parse shippings:", e);
            }
        }
    }

    // ————— Select2 Sales Order —————
    setupSoSelect() {
        this.soSelectEl
            .select2({
                theme: "bootstrap-5",
                placeholder: "Pilih Sales Order…",
                allowClear: true,
                ajax: {
                    url: routes.getSalesOrders(),
                    dataType: "json",
                    delay: 250,
                    data: (params) => ({
                        q: params.term,
                        page: params.page || 1,
                    }),
                    processResults: (data, params) => ({
                        results: data.results,
                        pagination: { more: data.pagination.more },
                    }),
                    cache: true,
                },
                templateResult: ({
                    loading,
                    no_so,
                    tanggal,
                    kode_customer,
                    nama_toko,
                }) =>
                    loading
                        ? "Mencari…"
                        : $(`
                        <div>
                        <strong>${no_so}</strong> – ${tanggal}<br>
                        <small class="text-muted">${kode_customer} | ${nama_toko}</small>
                        </div>
                    `),
                templateSelection: (d) => d.no_so || d.text,
            })
            .on("select2:select", async (e) => {
                const id = e.params.data.id;
                if (!id) return;

                try {
                    const res = await fetch(
                        route("transaksi_sales_orders.getSalesOrderDetail", {
                            salesOrder: id,
                        })
                    ).then((r) => r.json());

                    this.fillSoHeader(res);
                    this.fillSoDetails(res);
                } catch (err) {
                    console.error("Gagal load SO detail:", err);
                }
            })
            .on("select2:clear", () => {
                this.clearSoDetails();
            });
    }

    // ————— Select2 Area —————
    setupAreaSelect() {
        this.areaSelectEls
            .select2({
                theme: "bootstrap-5",
                placeholder: "Cari wilayah…",
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: routes.getArea(),
                    dataType: "json",
                    delay: 300,
                    data: (params) => ({ q: params.term }),
                    processResults: (data) => ({
                        results: data.areas.map((a) => ({
                            id: a.id,
                            text: a.name,
                        })),
                    }),
                    cache: true,
                },
            })
            .on("select2:select", function (e) {
                document.getElementById(`${this.id}_name`).value =
                    e.params.data.text;
            })
            .on("select2:clear", function () {
                document.getElementById(`${this.id}_name`).value = "";
            });
    }

    // ————— Hitung & tampilkan ongkir —————
    async checkOngkir() {
        const origin = document.getElementById("origin").value;
        const destination = document.getElementById("destination").value;

        const items = Array.from(document.querySelectorAll(".product-row"))
            .map((r) => ({
                name: r.dataset.nama,
                description: r.dataset.description,
                value: +r.dataset.value,
                length: +r.dataset.length,
                width: +r.dataset.width,
                height: +r.dataset.height,
                weight: +r.dataset.weight,
                quantity: +r.dataset.qty,
            }))
            .filter((i) => i.quantity > 0);

        if (!origin || !destination || items.length === 0) {
            return showToast(
                !origin || !destination
                    ? "Silakan pilih asal & tujuan pengiriman dulu."
                    : "Tidak ada item valid untuk menghitung ongkir.",
                "info"
            );
        }

        document.getElementById("harga_ongkir_list").innerHTML = `
      <div class="d-flex justify-content-center py-4">
        <div class="spinner-border" role="status"></div>
        <span class="ms-2">Loading ongkir…</span>
      </div>`;

        try {
            const token = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");

            const res = await fetch(routes.cekOngkir(), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    origin_area_id: origin,
                    destination_area_id: destination,
                    couriers: ["jne", "jnt", "sicepat", "anteraja"].join(","),
                    items,
                }),
            }).then((r) => r.json());

            if (!res.success || !res.pricing) throw new Error();
            this.renderOngkirTable(res.pricing);
            this.saveOngkirDataToForm(res.pricing);
        } catch {
            document.getElementById("harga_ongkir_list").innerHTML = `
        <div class="text-danger">Gagal mengambil ongkir.</div>`;
            this.saveOngkirDataToForm([]);
        }
    }

    renderOngkirTable(pricing = []) {
        const rows = pricing
            .map(
                (s) => `
      <tr>
        <td>${s.courier_name}</td>
        <td>${s.courier_service_name}</td>
        <td>${s.shipment_duration_range ?? "-"} hari</td>
        <td>${formatRupiah(s.price)}</td>
      </tr>
    `
            )
            .join("");

        document.getElementById("harga_ongkir_list").innerHTML = `
      <div class="table-responsive mt-2">
        <table class="table table-sm table-bordered">
          <thead class="table-light text-center">
            <tr><th>Kurir</th><th>Layanan</th><th>Estimasi</th><th>Harga</th></tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </div>`;
    }

    saveOngkirDataToForm(pricing = []) {
        const parsed = pricing.map((p) => ({
            courier_code: p.courier_code,
            courier_name: p.courier_name,
            courier_service_name: p.courier_service_name,
            shipment_duration_range: p.shipment_duration_range,
            price: p.price,
        }));
        const input = document.getElementById("shippings-data");
        if (input) input.value = JSON.stringify(parsed);
    }

    fillSoHeader(res) {
        const c = res.customer || {};
        document.getElementById("tanggal_so").textContent = res.tanggal || "-";
        document.getElementById("metode_pembayaran").textContent =
            res.metode_pembayaran || "-";
        document.getElementById("kode_customer").textContent =
            c.kode_customer || "-";
        document.getElementById("nama_toko").textContent = c.nama_toko || "-";
        document.getElementById("alamat").textContent = c.alamat || "-";
        document.getElementById("pemilik").textContent = c.pemilik || "-";
        document.getElementById("pasar").textContent = `(${
            c.id_pasar || "-"
        }) ${c.nama_pasar || "-"}`;
    }

    fillSoDetails(res) {
        let html = `
            <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light text-center">
                <tr>
                    <th>Produk</th><th>Kemasan</th><th>Harga</th>
                    <th>Qty</th><th>Diskon</th><th>Subtotal</th>
                </tr>
                </thead>
                <tbody>
        `;
        res.details.forEach((item) => {
            const h = parseFloat(item.harga);
            html += `
                <tr class="product-row"
                    data-product_id="${item.product_id}"
                    data-nama="${item.nama_produk}"
                    data-description="${item.kemasan}"
                    data-value="${h}"
                    data-length="${item.panjang || 10}"
                    data-width="${item.lebar || 10}"
                    data-height="${item.tinggi || 10}"
                    data-weight="${item.berat || 1000}"
                    data-qty="${item.qty}">
                    <!-- Hidden inputs tetap disertakan -->
                    <input type="hidden"
                        name="detail[${item.product_id}][product_id]"
                        value="${item.product_id}">
                    <td>
                    ${item.kode_produk} - ${item.nama_produk}
                    </td>
                    <td>
                    ${item.kemasan}
                    </td>
                    <td class="text-end">
                    ${formatRupiah(h)}
                    <input type="hidden"
                            name="detail[${item.product_id}][harga]"
                            value="${h}">
                    </td>
                    <td class="text-end">
                    ${formatNumber(item.qty)}
                    <input type="hidden"
                            name="detail[${item.product_id}][qty]"
                            value="${item.qty}">
                    </td>
                    <td class="text-end">
                    ${formatRupiah(item.diskon)}
                    <input type="hidden"
                            name="detail[${item.product_id}][diskon]"
                            value="${item.diskon}">
                    </td>
                    <td class="text-end">
                    ${formatRupiah(item.subtotal)}
                    <input type="hidden"
                            name="detail[${item.product_id}][subtotal]"
                            value="${item.subtotal}">
                    </td>
                </tr>
                `;
        });
        html += `
            </tbody>
            <tfoot>
            <!-- ...tfoot sama seperti sebelumnya... -->
            </tfoot>
        </table>
        </div>
    `;
        this.detailWrapper.innerHTML = html;
    }

    clearSoDetails() {
        this.detailWrapper.innerHTML = `
      <div class="text-center alert alert-warning rounded-4">
        Tidak ada detail yang ditampilkan.
      </div>`;
    }
}

export default new DeliveryOrdersPage();
