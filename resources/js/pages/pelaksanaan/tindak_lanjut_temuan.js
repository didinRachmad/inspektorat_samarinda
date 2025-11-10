import route from "@/routes";

const routes = {
    datatable: () => route("tindak_lanjut_temuan.data"),
};

class TindakLanjutTemuanPage {
    constructor() {
        this.pageName = "Tindak Lanjut Temuan";
        this.datatableEl = $("#datatables");
    }

    // === Index Page ===
    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();
        this.handleApprovalButtons();
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
        this.handleApprovalButtons();
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
                    title: "No",
                },
                { data: "id", name: "id", visible: false, title: "ID" },
                {
                    data: "nomor_lhp",
                    name: "nomor_lhp",
                    title: "Nomor LHP",
                },
                {
                    data: "nama_auditi",
                    name: "nama_auditi",
                    title: "Auditi",
                },
                {
                    data: "kode_nama_temuan",
                    name: "kode_nama_temuan",
                    title: "Nama Temuan",
                },
                {
                    data: "judul_temuan",
                    name: "judul_temuan",
                    title: "Judul Temuan",
                },
                // {
                //     data: "kondisi_temuan",
                //     name: "kondisi_temuan",
                //     title: "Kondisi Temuan",
                // },
                // {
                //     data: "kriteria_temuan",
                //     name: "kriteria_temuan",
                //     title: "Kriteria Temuan",
                // },
                // {
                //     data: "sebab_temuan",
                //     name: "sebab_temuan",
                //     title: "Sebab Temuan",
                // },
                // {
                //     data: "akibat_temuan",
                //     name: "akibat_temuan",
                //     title: "Akibat Temuan",
                // },
                {
                    data: "approval_status",
                    name: "approval_status",
                    className: "text-center",
                    title: "Status",
                    render: (data, type, row) => {
                        let badgeClass = "secondary";
                        let label = "-";
                        switch (data) {
                            case "draft":
                                badgeClass = "secondary";
                                label = "Draft";
                                break;
                            case "waiting":
                                badgeClass = "warning";
                                label = "Menunggu Approval";
                                break;
                            case "approved":
                                badgeClass = "success";
                                label = "Disetujui";
                                break;
                        }
                        return `<span class="badge bg-${badgeClass}">${label}</span>`;
                    },
                },
                {
                    data: "approval_note",
                    name: "approval_note",
                    title: "Note Approval",
                },
                {
                    data: "batas_waktu",
                    name: "batas_waktu",
                    className: "text-center",
                    title: "Batas Waktu",
                    render: (data, type, row) => {
                        if (!data) return "-";
                        return `<span class="badge ${
                            data.expired ? "bg-danger" : "bg-info"
                        }">
                ${data.formatted}
            </span>`;
                    },
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    title: "Aksi",
                    render: (data, type, row) => {
                        let buttons = "";

                        if (row.can_show) {
                            buttons += `
                <a href="${row.show_url}" class="btn btn-sm btn-info rounded-4" data-bs-toggle="tooltip" title="Detail">
                    <i class="bi bi-eye-fill"></i>
                </a>`;
                        }

                        if (row.can_approve || row.is_super_admin) {
                            if (row.approval_status === "draft") {
                                buttons += `
                    <button type="button" class="btn btn-sm btn-success rounded-4 btn-approve"
                        data-url="${row.approve_url}" data-action="approve" data-bs-toggle="tooltip"
                        title="Kirim untuk Approval"><i class="bi bi-send"></i></button>`;
                            } else {
                                buttons += `
                    <button type="button" class="btn btn-sm btn-success rounded-4 btn-approve"
                        data-url="${row.approve_url}" data-action="approve" data-bs-toggle="tooltip" title="Setujui"><i class="bi bi-check-circle"></i></button>
                    <button type="button" class="btn btn-sm btn-secondary rounded-4 btn-revise"
                        data-url="${row.approve_url}" data-action="revise" data-bs-toggle="tooltip" title="Revisi"><i class="bi bi-arrow-return-left"></i></button>`;
                            }
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
            drawCallback: () =>
                $('[data-bs-toggle="tooltip"]').each(function () {
                    new bootstrap.Tooltip(this);
                }),
        });
    }

    handleApprovalButtons() {
        $(document).on(
            "click",
            ".btn-approve, .btn-reject, .btn-revise",
            (e) => {
                const $btn = $(e.currentTarget);
                const url = $btn.data("url");
                const action = $btn.data("action");
                const redirect = $btn.data("redirect"); // ← baru

                const actionText =
                    action === "approve"
                        ? "Setujui"
                        : action === "reject"
                        ? "Tolak"
                        : "Revisi";

                showInputDialog(
                    `${actionText} Tindak Lanjut Temuan`,
                    "Tambahkan catatan (opsional):",
                    (note) => {
                        fetch(url, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                            body: JSON.stringify({
                                _method: "PATCH",
                                action,
                                note,
                            }),
                        })
                            .then((res) => res.json())
                            .then((data) => {
                                if (data.status) {
                                    showToast(data.message, "success");

                                    if (redirect) {
                                        // jika ada redirect → halaman show
                                        window.location.href = redirect;
                                    } else {
                                        // reload datatable
                                        this.datatableEl
                                            .DataTable()
                                            .ajax.reload(null, false);
                                    }
                                } else {
                                    showAlert(
                                        "Gagal",
                                        data.message || "Terjadi kesalahan",
                                        "error"
                                    );
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                showAlert(
                                    "Error",
                                    "Terjadi kesalahan pada server",
                                    "error"
                                );
                            });
                    }
                );
            }
        );
    }

    initForm() {}
}

export default new TindakLanjutTemuanPage();
