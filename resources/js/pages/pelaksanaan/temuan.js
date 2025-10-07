import route from "@/routes";

const routes = {
    datatable: () => route("temuan.data"),
};

class TemuanPage {
    constructor() {
        this.pageName = "Temuan";
        this.datatableEl = $("#datatables");
        this.rekomendasiTable = $("#rekomendasiTable");
        this.fileWrapper = $("#file-wrapper");
    }

    // === Index Page ===
    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
    }

    // === Create Page ===
    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initForm();
    }

    // === Edit Page ===
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initForm();
    }

    // === Show Page ===
    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
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
                { data: "lha_no", name: "lha_no" },
                { data: "judul_temuan", name: "judul_temuan" },
                { data: "kode_temuan", name: "kode_temuan" },
                {
                    data: "kondisi_temuan",
                    name: "kondisi_temuan",
                    render: window.renderSummernoteText,
                },
                {
                    data: "kriteria_temuan",
                    name: "kriteria_temuan",
                    render: window.renderSummernoteText,
                },
                {
                    data: "sebab_temuan",
                    name: "sebab_temuan",
                    render: window.renderSummernoteText,
                },
                {
                    data: "akibat_temuan",
                    name: "akibat_temuan",
                    render: window.renderSummernoteText,
                },
                // { data: "rekomendasi", name: "rekomendasi" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: (data, type, row) => {
                        let buttons = "";
                        if (row.can_edit) {
                            buttons += `<a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>`;
                        }
                        if (row.can_delete) {
                            buttons += `<form action="${
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

    // === Form Dinamis ===
    initForm() {
        const rekomTable = document
            .getElementById("rekomendasiTable")
            .getElementsByTagName("tbody")[0];

        // ambil baris pertama sebagai template
        const templateRekom = rekomTable.rows[0].cloneNode(true);

        // sembunyikan tombol hapus di template agar baris pertama tidak bisa dihapus
        templateRekom.querySelector(".remove-rekomendasi").style.display =
            "none";

        const addRekomBtn = document.getElementById("btnAddRekomendasi");

        addRekomBtn.addEventListener("click", () => {
            const newRow = templateRekom.cloneNode(true);
            const rowCount = rekomTable.rows.length;

            // reset select
            const select = newRow.querySelector(".rekom-select");
            if (select) {
                select.name = `rekomendasis[${rowCount}][kode_rekomendasi_id]`;
                select.selectedIndex = 0;
            }

            // reset input uraian
            const uraian = newRow.querySelector(".rekom-uraian");
            if (uraian) {
                uraian.name = `rekomendasis[${rowCount}][rekomendasi_temuan]`;
                uraian.value = "";
            }

            // reset input nominal
            const nominal = newRow.querySelector(".rekom-nominal");
            if (nominal) {
                nominal.name = `rekomendasis[${rowCount}][nominal]`;
                nominal.value = 0;
            }

            // tombol hapus
            const btnRemove = newRow.querySelector(".remove-rekomendasi");
            if (btnRemove) {
                btnRemove.style.display = "inline-block";
                btnRemove.addEventListener("click", () => newRow.remove());
            }

            rekomTable.appendChild(newRow);
        });

        // ===== File Dinamis =====
        const fileTable = document
            .getElementById("fileTable")
            .getElementsByTagName("tbody")[0];
        const btnAddFile = document.getElementById("btnAddFile");
        const templateFile = fileTable.querySelector("tr:first-child");

        btnAddFile.addEventListener("click", () => {
            const newRow = templateFile.cloneNode(true);

            const inputFile = newRow.querySelector("input[type=file]");
            inputFile.value = "";
            inputFile.name = "files[]";

            const btnRemove = newRow.querySelector(".remove-file");
            btnRemove.style.display = "inline-block";
            btnRemove.addEventListener("click", () => newRow.remove());

            fileTable.appendChild(newRow);
        });
    }
}

export default new TemuanPage();
