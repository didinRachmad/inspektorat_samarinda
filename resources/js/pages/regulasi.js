import route from "@/routes";

const routes = {
    datatable: () => route("regulasi.data"),
    store: () => route("regulasi.store"),
    update: (id) => route("regulasi.update", { regulasi: id }),
};

class RegulasiPage {
    constructor() {
        this.pageName = "Master Regulasi";
        this.datatableEl = $("#datatables");
        this.formEl = document.getElementById("regulasiForm");

        this.modalEl = document.getElementById("regulasiModal");
        this.modal = new bootstrap.Modal(this.modalEl);

        this.titleInput = document.getElementById("title");
        this.descriptionInput = document.getElementById("description");
        this.fileInput = document.getElementById("file");
        this.regulasiIdInput = document.getElementById("regulasi_id");
        this.currentFileText = document.getElementById("currentFile");

        // tombol tambah
        const btnTambah = document.getElementById("btnTambahRegulasi");
        if (btnTambah) {
            btnTambah.addEventListener("click", () => this.resetModal());
        }
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
        this.initFormSubmit();
    }

    initDataTable() {
        const self = this;

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
                { data: "title", name: "title" },
                { data: "description", name: "description" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: (data, type, row) => {
                        let buttons = `<a href="${row.download_url}" class="btn btn-sm btn-success rounded-4" data-bs-toggle="tooltip" title="Download"><i class="bi bi-download"></i></a>`;

                        if (row.can_edit) {
                            buttons += `<button type="button" class="btn btn-sm btn-warning rounded-4 btn-edit"
                                data-id="${row.id}"
                                data-title="${row.title}"
                                data-description="${row.description}"
                                data-file="${row.download_url || ""}"
                                data-file_path="${row.file_path || ""}"
                                data-bs-toggle="tooltip"
                                title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>`;
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
                                </form>
                            `;
                        }

                        return `<div class="d-flex justify-content-center gap-1">${buttons}</div>`;
                    },
                },
            ],
            dom:
                "<'row'<'col-md-3'l><'col-md-6 text-center'><'col-md-3'f>>" +
                "<'row table-responsive py-2'<'col-sm-12'tr>>" +
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
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                });

                // event delegation tombol edit
                $(".btn-edit")
                    .off("click")
                    .on("click", function () {
                        const btn = $(this);
                        self.fillEditModal({
                            id: btn.data("id"),
                            title: btn.data("title"),
                            description: btn.data("description"),
                            file: btn.data("file"),
                            file_path: btn.data("file_path"),
                        });
                    });
            },
        });
    }

    resetModal() {
        this.formEl.reset();
        this.regulasiIdInput.value = "";
        this.currentFileText.textContent = "";
        this.modalEl.querySelector(".modal-title").textContent =
            "Tambah Regulasi";
        this.modal.show();
    }

    fillEditModal(data) {
        this.regulasiIdInput.value = data.id;
        this.titleInput.value = data.title;
        this.descriptionInput.value = data.description;

        if (data.file_path) {
            // Ambil nama file dari path terakhir
            const fileName = data.file_path.split("/").pop();
            this.currentFileText.innerHTML = `<a href="${data.file}" target="_blank">${fileName}</a>`;
        } else {
            this.currentFileText.textContent = "";
        }

        this.modalEl.querySelector(".modal-title").textContent =
            "Edit Regulasi";
        this.modal.show();
    }

    initFormSubmit() {
        const self = this;
        this.formEl.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const id = self.regulasiIdInput.value;
            let url = id ? routes.update(id) : routes.store();
            if (id) formData.append("_method", "PUT");

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    self.modal.hide();
                    self.datatableEl.DataTable().ajax.reload();
                    showToast(
                        res.message || "Regulasi berhasil disimpan.",
                        "success"
                    );
                },
                error: function (err) {
                    console.error(err);
                    showAlert(
                        "Gagal",
                        err.responseJSON?.message || "Terjadi kesalahan",
                        "error"
                    );
                },
            });
        });
    }
}

const regulasiPage = new RegulasiPage();
window.RegulasiPage = regulasiPage; // Supaya bisa diakses global jika perlu
export default regulasiPage;
