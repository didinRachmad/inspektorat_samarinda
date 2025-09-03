import route from "@/routes";

const routes = {
    datatable: () => route("approval_routes.data"),
    getUsersByRole: (id) => route("users.getUsersByRole", { role: id }),
};

class ApprovalRoutesPage {
    constructor() {
        this.pageName = "Approval Routes";
        this.datatableEl = $("#datatables");
        this.moduleSelectEl = $("#module");
        this.roleSelectEl = $("#role_id");
        this.userSelectEl = $("#assigned_user_id");
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
        this.initSelect2();
        this.handleRoleChange();
    }

    // Halaman Edit
    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initSelect2();
        this.handleRoleChange();

        if (this.roleSelectEl.val()) {
            this.roleSelectEl.trigger("change");
        }
    }

    // ——— Inisialisasi DataTable ———
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
                { data: "module", name: "module" },
                { data: "role", name: "roles.name" },
                { data: "sequence", name: "sequence" },
                {
                    data: "assigned_user",
                    name: "users.email",
                    render: (data) =>
                        data ? data : '<span class="text-muted">-</span>',
                },
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
                    sSortAscending: ": aktifkan untuk mengurutkan kolom menaik",
                    sSortDescending:
                        ": aktifkan untuk mengurutkan kolom menurun",
                },
            },
            drawCallback: () => {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                });
            },
        });
    }

    // ——— Inisialisasi Select2 ———
    initSelect2() {
        this.moduleSelectEl.select2({
            placeholder: "Pilih Module",
            allowClear: true,
            theme: "bootstrap-5",
            width: "100%",
        });

        this.roleSelectEl.select2({
            placeholder: "Pilih Role",
            allowClear: true,
            theme: "bootstrap-5",
            width: "100%",
        });

        this.userSelectEl.select2({
            placeholder: "Pilih User",
            allowClear: true,
            theme: "bootstrap-5",
            width: "100%",
            disabled: true,
        });
    }

    // ——— Saat Role berubah, load user ———
    handleRoleChange() {
        this.roleSelectEl.on("change", () => {
            const roleId = this.roleSelectEl.val();
            this.userSelectEl
                .val(null)
                .trigger("change")
                .prop("disabled", !roleId);

            if (roleId) {
                this.loadUserOptions(roleId);
            }
        });
    }

    // ——— Ambil user dari role ———
    loadUserOptions(roleId) {
        this.userSelectEl.select2("destroy");
        this.userSelectEl.empty().trigger("change");

        this.userSelectEl.select2({
            placeholder: "Pilih User",
            allowClear: true,
            theme: "bootstrap-5",
            width: "100%",
            ajax: {
                url: routes.getUsersByRole(roleId),
                delay: 250,
                data: (params) => ({
                    search: params.term,
                    page: params.page || 1,
                }),
                processResults: (data, params) => ({
                    results: data.data,
                    pagination: {
                        more: data.current_page < data.last_page,
                    },
                }),
            },
        });

        // Preselect user saat edit
        const selectedId = this.userSelectEl.data("selected-id");
        const selectedEmail = this.userSelectEl.data("selected-email");

        if (selectedId && selectedEmail) {
            const option = new Option(selectedEmail, selectedId, true, true);
            this.userSelectEl.append(option).trigger("change");
        }
    }
}

export default new ApprovalRoutesPage();
