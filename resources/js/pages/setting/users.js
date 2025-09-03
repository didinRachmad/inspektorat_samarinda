import route from "@/routes";

const routes = {
    datatable: () => route("users.data"),
    getRoles: () => route("roles.getRoles"),
};

class UsersPage {
    constructor() {
        this.pageName = "Setting Users";
        this.table = $("#datatables");
        this.roleSelect = $("#role_id");
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
    }

    initShow() {
        console.log(`Halaman ${this.pageName} Show berhasil dimuat!`);
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initRoleSelect();
    }

    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initRoleSelect();
    }

    initDataTable() {
        this.table.DataTable({
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
                { data: "name", name: "name" },
                { data: "email", name: "email" },
                { data: "roles", name: "roles" },
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
                                </a> `;
                        }

                        if (row.can_reset_password) {
                            buttons += `
                                <form action="${
                                    row.reset_password_url
                                }" method="POST" class="d-inline form-reset-password">
                                    <input type="hidden" name="_token" value="${$(
                                        'meta[name="csrf-token"]'
                                    ).attr("content")}">
                                    <button type="button" class="btn btn-sm btn-secondary rounded-4 btn-reset-password" data-bs-toggle="tooltip" title="Reset Password">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>`;
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
            columnDefs: [
                {
                    targets: 0,
                    className: "text-center",
                },
            ],
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

    initRoleSelect() {
        if (!this.roleSelect.length) return;

        this.roleSelect.select2({
            theme: "bootstrap-5",
            placeholder: "Pilih role…",
            allowClear: true,
            width: "100%",
            selectionCssClass: "select2--small",
            dropdownCssClass: "select2--small",
            ajax: {
                url: routes.getRoles(),
                dataType: "json",
                delay: 250,
                data: (params) => ({
                    q: params.term,
                }),
                processResults: (data) => ({
                    results: data.map((role) => ({
                        id: role.id,
                        text: role.name,
                    })),
                }),
                cache: true,
            },
        });
    }
}

export default new UsersPage();
