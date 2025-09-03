import route from "@/routes";
import AutoNumeric from "autonumeric";

const routes = {
    datatable: () => route("pkpt.data"),
};

class PkptPage {
    constructor() {
        this.pageName = "Setting PKPT";
        this.datatableEl = $("#datatables");
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
        this.handleDelete(); // register handler delete
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initForm();
    }

    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initForm();
    }

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
                { data: "tahun", name: "tahun", className: "text-center" },
                { data: "no_pkpt", name: "no_pkpt" },
                { data: "auditi", name: "auditi" },
                { data: "ruang_lingkup", name: "ruang_lingkup" },
                { data: "sasaran", name: "sasaran" },
                {
                    data: "anggaran_total",
                    name: "anggaran_total",
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", ",", 0, "Rp "),
                },
                { data: "pj", name: "pj", className: "text-center" },
                { data: "wpj", name: "wpj", className: "text-center" },
                { data: "pt", name: "pt", className: "text-center" },
                { data: "kt", name: "kt", className: "text-center" },
                { data: "at", name: "at", className: "text-center" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: (data, type, row) => {
                        let buttons = "";
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
                "<'row py-2'<'col-sm-12 table-responsive'tr>>" +
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
                sInfoEmpty: "Menampilkan 0 hingga 0 dari 0 entri",
                sInfoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
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
            drawCallback: () => {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                });
            },
        });
    }

    // handler delete
    handleDelete() {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest(".btn-delete");
            if (!btn) return;
            if (confirm("Yakin hapus data ini?")) {
                btn.closest("form").submit();
            }
        });
    }

    initForm() {
        this.hitungTotalAnggaran();

        document
            .querySelector("#jabatanTable")
            .addEventListener("input", (e) => {
                if (e.target.matches(".anggaran")) {
                    this.hitungTotalAnggaran();
                }
            });
    }

    hitungTotalAnggaran() {
        let total = 0;

        document.querySelectorAll("#jabatanTable tbody tr").forEach((row) => {
            const anggaran =
                AutoNumeric.getAutoNumericElement(
                    row.querySelector(".anggaran")
                )?.getNumber() ?? 0;
            total += anggaran;
        });
        console.log(total);

        const totalInput = document.getElementById("totalAnggaran");
        if (totalInput) {
            const totalAN = AutoNumeric.getAutoNumericElement(totalInput);
            if (totalAN) {
                totalAN.set(total);
            } else {
                totalInput.value = total;
            }
        }
    }
}

export default new PkptPage();
