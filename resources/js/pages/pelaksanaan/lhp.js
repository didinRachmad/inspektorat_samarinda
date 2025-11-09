import route from "@/routes";

const routes = {
    datatable: () => route("lhp.data"),
    kkaStore: () => route("kka.store"), // route untuk simpan KKA
    kkaUpdate: (id) => route("kka.update", id), // route update KKA
};

class LhpPage {
    constructor() {
        this.pageName = "LHP";
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
        this.handleAddKka();
        this.handleSubmitKka();
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
                { data: "pkpt_no", name: "pkpt_no", title: "PKPT No" },
                {
                    data: "pkpt_sasaran",
                    name: "pkpt_sasaran",
                    title: "PKPT Sasaran",
                },
                { data: "nomor_lhp", name: "nomor_lhp", title: "Nomor LHP" },
                {
                    data: "auditi",
                    name: "auditi",
                    title: "Auditi",
                },
                {
                    data: "tanggal_lhp",
                    name: "tanggal_lhp",
                    className: "text-center",
                    title: "Tanggal",
                },
                {
                    data: "rekomendasi",
                    name: "rekomendasi",
                    title: "Rekomendasi",
                },
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
                            case "rejected":
                                badgeClass = "danger";
                                label = "Ditolak";
                                break;
                        }

                        return `<span class="badge bg-${badgeClass}">${label}</span>`;
                    },
                },
                {
                    data: "next_approver",
                    name: "next_approver",
                    title: "Approval",
                },
                {
                    data: "approval_note",
                    name: "approval_note",
                    title: "Notes Approval",
                    render: (data, type, row) => {
                        return data ? data : "-";
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

                        if (row.can_edit) {
                            buttons += `
                <a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" data-bs-toggle="tooltip" title="Edit">
                    <i class="bi bi-pencil-square"></i>
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
                        data-url="${row.approve_url}" data-action="revise" data-bs-toggle="tooltip" title="Revisi"><i class="bi bi-arrow-return-left"></i></button>
                    <button type="button" class="btn btn-sm btn-danger rounded-4 btn-reject"
                        data-url="${row.approve_url}" data-action="reject" data-bs-toggle="tooltip" title="Tolak"><i class="bi bi-x-circle"></i></button>`;
                            }
                        }

                        if (row.can_delete && row.approval_status === "draft") {
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
                    `${actionText} LHP`,
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

    initForm() {
        const populateAuditiSelect = (
            selectedPkptId,
            selectedAuditiId = null
        ) => {
            if (!selectedPkptId) return;

            const pkptOption = $(
                `#pkptSelect option[value="${selectedPkptId}"]`
            );
            const auditis = pkptOption.data("auditis") || [];

            const $auditiSelect = $("#auditiSelect");
            $auditiSelect
                .empty()
                .append('<option value="">-- pilih auditi --</option>');

            auditis.forEach((a) => {
                const selected = a.id == selectedAuditiId ? "selected" : "";
                $auditiSelect.append(
                    `<option value="${a.id}" ${selected}>${a.nama_auditi}</option>`
                );
            });

            $auditiSelect.trigger("change");
        };

        // ambil default dari HTML
        const oldPkptId = $("#pkptSelect").data("selected");
        const oldAuditiId = $("#pkptSelect").data("selected-auditi");

        // populate saat load
        if (oldPkptId) populateAuditiSelect(oldPkptId, oldAuditiId);

        // saat user ganti PKPT
        $("#pkptSelect").on("change", function () {
            const pkptId = $(this).val();
            populateAuditiSelect(pkptId);
        });

        // ====== TEMUAN ======

        let temuanIndex = $("#temuanContainer .temuan-item").length;

        // 🔰 Fungsi reusable untuk tambah temuan baru
        function addTemuan() {
            let html = $("#temuanTemplate")
                .html()
                .replaceAll("[i]", temuanIndex)
                .replaceAll("[index]", temuanIndex + 1);

            let $template = $(html);
            $("#temuanContainer").append($template);
            reinitPlugins($template);

            // update rekomendasi otomatis saat temuan baru ditambahkan
            updateRekomendasiOptions($template);

            // inisialisasi counter file baru
            $template.data("fileCounter", 0);

            temuanIndex++;
        }

        // 🔵 Tambah Temuan Pertama Saat Halaman Load
        if ($("#temuanContainer .temuan-item").length === 0) {
            addTemuan();
        }

        // 🔵 Tombol Tambah Temuan
        $("#btnAddTemuan").on("click", () => addTemuan());

        // 🔴 Hapus Temuan
        $(document).on("click", ".remove-temuan", function () {
            const $temuanItem = $(this).closest(".temuan-item");
            const totalTemuan = $("#temuanContainer .temuan-item").length;

            if (totalTemuan <= 1) {
                showAlert(
                    "Tidak bisa dihapus!",
                    "Minimal harus ada satu temuan.",
                    "warning"
                );
                return;
            }

            const temuanTitle = $temuanItem.find("h6").text();
            showConfirmDialog(
                `Hapus ${temuanTitle}?`,
                "Data temuan ini akan dihapus.",
                () => {
                    $temuanItem.remove();
                    reindexAll();
                    showAlert(
                        "Berhasil!",
                        "Temuan berhasil dihapus.",
                        "success"
                    );
                },
                "delete"
            );
        });

        // 🔵 Tambah File Pendukung per Temuan (pakai template)
        $(document).on("click", ".btnAddFile", function () {
            const $temuan = $(this).closest(".temuan-item");
            const $tbody = $temuan.find(".filePendukung tbody");

            // ambil fileCounter dari data temuan
            let fileCounter =
                $temuan.data("fileCounter") || $tbody.find("tr").length;

            let temuanIndex = $temuan.index(); // index temuan
            let html = $("#fileTemplate")
                .html()
                .replaceAll("[i]", temuanIndex) // temuan index
                .replaceAll("[j]", fileCounter); // file index

            $tbody.append(html);

            fileCounter++;
            $temuan.data("fileCounter", fileCounter);
        });

        // 🔴 Hapus File Pendukung
        $(document).on("click", ".remove-file", function () {
            const $row = $(this).closest("tr");
            const $tbody = $(this).closest("tbody");
            const total = $tbody.find("tr").length;

            if (total <= 1) {
                showAlert(
                    "Tidak bisa dihapus!",
                    "Minimal satu file harus ada.",
                    "warning"
                );
                return;
            }

            const fileId = $row.find("input[type='hidden']").val();
            if (fileId)
                $tbody.append(
                    `<input type="hidden" name="deleted_files[]" value="${fileId}">`
                );
            $row.remove();
        });

        // 🔵 Tambah Rekomendasi per Temuan
        $(document).on("click", ".btnAddRekomendasi", function () {
            const $temuan = $(this).closest(".temuan-item");
            const temuanIndex = $temuan.index();
            const $tbody = $temuan.find(".rekomendasiTable tbody");
            const rekomendasiIndex = $tbody.find("tr").length;

            let html = $("#rekomendasiTemplate")
                .html()
                .replaceAll("[i]", temuanIndex)
                .replaceAll("[j]", rekomendasiIndex);

            $tbody.append(html);
            reinitPlugins($tbody.find("tr").last());
            updateRekomendasiOptions($temuan);
        });

        // 🔴 Hapus Rekomendasi
        $(document).on("click", ".remove-rekomendasi", function () {
            const $tbody = $(this).closest("tbody");
            const total = $tbody.find("tr").length;

            if (total <= 1) {
                showAlert(
                    "Tidak bisa dihapus!",
                    "Minimal satu rekomendasi harus ada.",
                    "warning"
                );
                return;
            }

            $(this).closest("tr").remove();
            reindexRekomendasi($(this).closest(".temuan-item"));
        });

        // 🔹 Update rekomendasi otomatis
        function updateRekomendasiOptions($temuanItem) {
            const selectedId = $temuanItem.find(".kode-temuan-select").val();
            const mapping = $temuanItem
                .find(".kode-temuan-select")
                .data("rekom");

            $temuanItem.find(".rekomendasiTable tbody tr").each(function () {
                const $select = $(this).find("select");
                const oldVal = $select.val();
                $select
                    .empty()
                    .append(
                        '<option value="">-- Pilih Kode Rekomendasi --</option>'
                    );

                if (selectedId && mapping[selectedId]) {
                    mapping[selectedId].forEach((r) => {
                        $select.append(
                            `<option value="${r.id}" ${
                                r.id == oldVal ? "selected" : ""
                            }>${r.kode} | ${r.nama_rekomendasi}</option>`
                        );
                    });
                }
            });
        }

        $("#temuanContainer .temuan-item").each(function () {
            updateRekomendasiOptions($(this));
        });

        $(document).on("change", ".kode-temuan-select", function () {
            const $temuanItem = $(this).closest(".temuan-item");
            updateRekomendasiOptions($temuanItem);
        });

        // 🔹 Reindex semua temuan & rekomendasi
        function reindexAll() {
            $("#temuanContainer .temuan-item").each(function (i) {
                $(this)
                    .find("h6")
                    .text(`Temuan #${i + 1}`);

                $(this)
                    .find("[name]")
                    .not('input[type="file"]')
                    .each(function () {
                        let oldName = $(this).attr("name");
                        oldName = oldName.replace(
                            /\[temuans]\[\d+]/,
                            `[temuans][${i}]`
                        );
                        $(this).attr("name", oldName);
                    });

                reindexRekomendasi($(this));
            });

            temuanIndex = $("#temuanContainer .temuan-item").length;
        }

        function reindexRekomendasi($temuan) {
            const temuanIndex = $temuan.index();
            $temuan.find(".rekomendasiTable tbody tr").each(function (j) {
                $(this)
                    .find("[name]")
                    .each(function () {
                        let oldName = $(this).attr("name");
                        oldName = oldName
                            .replace(
                                /\[temuans]\[\d+]/,
                                `[temuans][${temuanIndex}]`
                            )
                            .replace(
                                /\[rekomendasis]\[\d+]/,
                                `[rekomendasis][${j}]`
                            );
                        $(this).attr("name", oldName);
                    });
            });
        }
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

export default new LhpPage();
