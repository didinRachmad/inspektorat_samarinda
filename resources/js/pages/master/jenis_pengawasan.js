import route from "@/routes";

const routes = {
    datatable: () => route("jenis_pengawasan.data"),
};

class JenisPengawasanPage {
    constructor() {
        this.pageName = "Master Jenis Pengawasan";
        this.datatableEl = $("#datatables");
        this.childIndex = $("#child-items .child-item").length || 0;
    }

    // Halaman Index
    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
    }

    // Halaman Show
    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
    }

    // Halaman Create
    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initForm();
    }

    // Halaman Edit
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initForm();
    }

    // Inisialisasi DataTable
    initDataTable() {
        this.datatableEl.DataTable({
            processing: true,
            serverSide: true,
            ajax: routes.datatable(),
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex" },
                { data: "nama", name: "nama" },
                { data: "urutan", name: "urutan" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: function (data, type, row) {
                        let buttons = "";

                        if (row.can_edit) {
                            buttons += `
                                <a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
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
            order: [[2, "asc"]], // urut berdasarkan urutan
            info: true,
            language: {
                sEmptyTable: "Tidak ada data yang tersedia di tabel",
                sInfo: "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                sInfoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
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

    // Inisialisasi Tambah/Hapus Child Dinamis
    initForm() {
        const self = this;

        $("#add-child").on("click", function () {
            let html = `
            <div class="row mb-2 child-item">
                <div class="col-md-6">
                    <input type="text" name="children[${self.childIndex}][nama]" class="form-control form-control-sm" placeholder="Nama Sub Item" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="children[${self.childIndex}][urutan]" class="form-control form-control-sm" placeholder="Urutan" value="1" min="1" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-child">Hapus <i class="bi bi-trash-fill"></i></button>
                </div>
            </div>`;
            $("#child-items").append(html);
            self.childIndex++;
        });

        $(document).on("click", ".remove-child", function () {
            $(this).closest(".child-item").remove();
        });
    }
}

export default new JenisPengawasanPage();
