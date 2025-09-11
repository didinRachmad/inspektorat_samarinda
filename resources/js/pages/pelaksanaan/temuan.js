import route from "@/routes";

const routes = {
    datatable: () => route("temuan.data"),
    kkaStore: () => route("kka.store"), // route untuk simpan KKA
    kkaUpdate: (id) => route("kka.update", id), // route update KKA
};

class TemuanPage {
    constructor() {
        this.pageName = "Temuan";
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
        this.handleRekomendasi();
        this.handleFiles();
    }

    // === Edit Page ===
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.handleRekomendasi();
        this.handleFiles();
    }

    // === Show Page ===
    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
        this.handleAddKka();
        this.handleSubmitKka();
    }

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
                { data: "lha_no", name: "lha_no" }, // nomor LHA
                { data: "judul_temuan", name: "judul_temuan" }, // judul temuan
                { data: "kode_temuan", name: "kode_temuan" }, // kode temuan
                { data: "kondisi_temuan", name: "kondisi_temuan" }, // kondisi temuan
                { data: "kriteria_temuan", name: "kriteria_temuan" }, // kriteria temuan
                { data: "sebab_temuan", name: "sebab_temuan" }, // sebab temuan
                { data: "akibat_temuan", name: "akibat_temuan" }, // akibat temuan
                {
                    data: "rekomendasi",
                    name: "rekomendasi",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: (data, type, row) => {
                        let buttons = "";

                        if (row.can_show) {
                            buttons += `
        <a href="${row.show_url}" class="btn btn-sm btn-info rounded-4" data-bs-toggle="tooltip" title="Detail">
            <i class="bi bi-eye-fill"></i>
        </a>`;
                        }

                        if (row.can_edit) {
                            buttons += `
        <a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
            <i class="bi bi-pencil-square"></i>
        </a>`;
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

    // === Handler Rekomendasi Dinamis ===
    handleRekomendasi() {
        const wrapper = document.getElementById("rekomendasi-wrapper");
        if (!wrapper) return;

        wrapper.addEventListener("click", (e) => {
            // Tambah rekomendasi
            if (e.target.classList.contains("add-rekomendasi")) {
                e.preventDefault();
                const items = wrapper.querySelectorAll(".rekomendasi-item");
                const lastItem = items[items.length - 1];
                const newIndex = items.length;

                const newItem = lastItem.cloneNode(true);

                // Reset value input
                newItem.querySelectorAll("input").forEach((input) => {
                    input.value = "";
                    input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                });

                // Ubah tombol di newItem menjadi remove
                const btn = newItem.querySelector(".btn");
                btn.classList.remove("btn-success", "add-rekomendasi");
                btn.classList.add("btn-danger", "remove-rekomendasi");
                btn.textContent = "-";

                wrapper.appendChild(newItem);
            }

            // Hapus rekomendasi
            if (e.target.classList.contains("remove-rekomendasi")) {
                e.preventDefault();
                const item = e.target.closest(".rekomendasi-item");
                if (item) item.remove();
            }
        });
    }

    // === Handler File Dinamis ===
    handleFiles() {
        const wrapper = document.getElementById("file-wrapper");
        if (!wrapper) return;

        wrapper.addEventListener("click", (e) => {
            // Tambah file
            if (e.target.classList.contains("add-file")) {
                e.preventDefault();
                const items = wrapper.querySelectorAll(".file-item");
                const lastItem = items[items.length - 1];

                const newItem = lastItem.cloneNode(true);
                newItem.querySelector("input").value = "";

                // ubah tombol + menjadi tombol remove
                const btn = newItem.querySelector(".btn");
                btn.classList.remove("btn-success", "add-file");
                btn.classList.add("btn-danger", "remove-file");
                btn.textContent = "-";

                wrapper.appendChild(newItem);
            }

            // Hapus file
            if (e.target.classList.contains("remove-file")) {
                e.preventDefault();
                const item = e.target.closest(".file-item");
                if (item) item.remove();
            }
        });
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

export default new TemuanPage();
