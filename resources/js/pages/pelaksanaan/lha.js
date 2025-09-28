import route from "@/routes";

const routes = {
    datatable: () => route("lha.data"),
    kkaStore: () => route("kka.store"), // route untuk simpan KKA
    kkaUpdate: (id) => route("kka.update", id), // route update KKA
};

class LhaPage {
    constructor() {
        this.pageName = "LHA";
        this.datatableEl = $("#datatables");
        this.modalEl = $("#modalKka");
        this.modal = this.modalEl.length
            ? new bootstrap.Modal(this.modalEl[0])
            : null;
        this.form = $("#formKka");
        this.listWrapper = $("#kkaList");
    }

    // === Index Page ===
    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
    }

    // === Create Page ===
    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initAuditFields();
    }

    // === Edit Page ===
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initAuditFields();
    }

    // === Show Page ===
    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
        this.handleAddKka();
        this.handleSubmitKka();
    }

    // === Datatable ===
    // === Datatable ===
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
                { data: "pkpt_no", name: "pkpt_no" },
                { data: "pkpt_auditi", name: "pkpt_auditi" },
                { data: "pkpt_sasaran", name: "pkpt_sasaran" },
                { data: "nomor_lha", name: "nomor_lha" },
                {
                    data: "tanggal_lha",
                    name: "tanggal_lha",
                    className: "text-center",
                },
                { data: "rekomendasi", name: "rekomendasi" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: (data, type, row) => {
                        let buttons = "";

                        // tombol show
                        if (row.can_show) {
                            buttons += `
                <a href="${row.show_url}" class="btn btn-sm btn-info rounded-4" data-bs-toggle="tooltip" title="Detail">
                    <i class="bi bi-eye-fill"></i>
                </a>`;
                        }

                        // tombol edit
                        if (row.can_edit) {
                            buttons += `
                <a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </a>`;
                        }

                        // tombol delete
                        if (row.can_delete) {
                            buttons += `
                <form action="${
                    row.delete_url
                }" method="POST" class="d-inline form-delete">
                    <input type="hidden" name="_token" value="${$(
                        'meta[name="csrf-token"]'
                    ).attr("content")}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-sm btn-danger rounded-4 btn-delete" data-bs-toggle="tooltip" title="Hapus">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </form>`;
                        }

                        return `<div class="d-flex justify-content-center gap-1">${buttons}</div>`;
                    },
                },
            ],
            dom:
                "<'row'<'col-md-3'l><'col-md-6 text-center'><'col-md-3'f>>" +
                "<'row py-2'<'col-sm-12'tr>>" +
                "<'row'<'col-md-5'i><'col-md-7'p>>",
            paging: true,
            responsive: true,
            pageLength: 20,
            lengthMenu: [
                [20, 50, -1],
                [20, 50, "Semua"],
            ],
            order: [[1, "desc"]],
            info: true,
            language: {
                sEmptyTable: "Tidak ada data yang tersedia di tabel",
                sInfo: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                sLengthMenu: "Tampilkan _MENU_ entri",
                sLoadingRecords: "Memuat...",
                sProcessing: "Sedang memproses...",
                sSearch: "Cari:",
                sZeroRecords: "Tidak ditemukan data yang cocok",
                oAria: {
                    sSortAscending: ": aktifkan untuk mengurutkan kolom menaik",
                    sSortDescending:
                        ": aktifkan untuk mengurutkan kolom menurun",
                },
            },
            drawCallback: () =>
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                }),
        });
    }

    initAuditFields() {
        const pkptSelect = document.getElementById("pkptSelect");
        const auditFields = document.getElementById("auditFields");
        if (!pkptSelect || !auditFields) return;

        const toggleAuditFields = () => {
            const selected = pkptSelect.options[pkptSelect.selectedIndex];
            const jenis = selected?.getAttribute("data-jenis") || "";
            if (jenis === "AUDIT") {
                auditFields.style.display = "block";
            } else {
                auditFields.style.display = "none";
            }
        };

        pkptSelect.addEventListener("change", toggleAuditFields);
        toggleAuditFields(); // jalankan saat load (misalnya edit mode)
    }

    // === Handler untuk tombol Tambah KKA di Show ===
    handleAddKka() {
        let btnAdd = document.getElementById("btnAddKka");
        if (!btnAdd) return;

        btnAdd.addEventListener("click", () => {
            this.form.trigger("reset"); // versi jQuery
            this.modalEl.find(".modal-title").text("Tambah KKA");
            this.modal.show();
        });
    }

    // === Submit Modal Form KKA ===
    handleSubmitKka() {
        if (!this.form.length) return;

        // klik tombol simpan akan trigger submit form biasa
        $("#btnSaveKka").on("click", () => {
            this.form.submit(); // langsung submit, biarkan Laravel handle
        });
    }
}

export default new LhaPage();
