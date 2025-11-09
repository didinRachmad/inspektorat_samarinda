import route from "@/routes";
import AutoNumeric from "autonumeric";
import axios from "axios";

const routes = {
    datatable: () => route("non_pkpt.data"),
    getAnggaran: () => route("setting_anggaran.getAnggaran"),
};

class NonPkptPage {
    constructor() {
        this.pageName = "Non PKPT";
        this.datatableEl = $("#datatables");
        this.anggaranPerHari = 170000;
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} Index berhasil dimuat!`);
        this.initDataTable();

        // filter reload
        $(
            "#filterMandatory, #filterBulan, #filterTahun, #filterAuditi, #filterIrbanwil, #filterJenisPengawasan"
        ).on("change", () => {
            $("#datatables").DataTable().ajax.reload();
        });
    }

    initCreate() {
        console.log(`Halaman ${this.pageName} Create berhasil dimuat!`);
        this.initForm();
    }

    initEdit() {
        console.log(`Halaman ${this.pageName} Edit berhasil dimuat!`);
        this.initForm();
    }

    /**
     * -------------------------------
     * Datatable
     * -------------------------------
     */
    initDataTable() {
        this.datatableEl.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: routes.datatable(),
                type: "GET",
                data: function (d) {
                    d.bulan = $("#filterBulan").val();
                    d.tahun = $("#filterTahun").val();
                    d.mandatory_id = $("#filterMandatory").val();
                    d.auditi_id = $("#filterAuditi").val();
                    d.irbanwil_id = $("#filterIrbanwil").val();
                    d.jenis_pengawasan_id = $("#filterJenisPengawasan").val();
                },
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                },
                { data: "id", name: "id", visible: false },
                { data: "tahun", name: "tahun", className: "text-center" },
                { data: "bulan", name: "bulan", className: "text-center" },
                { data: "no_pkpt", name: "no_pkpt" },
                { data: "mandatory_nama", name: "mandatories.nama" },
                {
                    data: "auditi_list",
                    render: function (data, type, row, meta) {
                        if (type === "display") {
                            if (!data) return "";
                            let items = data.split(",").map((a) => a.trim());
                            let html =
                                '<ul class="m-0" style="padding-left: 15px; margin:0;">';
                            items.forEach((a) => (html += `<li>${a}</li>`));
                            html += "</ul>";
                            return html;
                        }
                        // Untuk sort/search, kembalikan data murni
                        return data;
                    },
                },
                { data: "sasaran", name: "sasaran" },
                {
                    data: "ruang_lingkup",
                    name: "ruang_lingkup",
                    render: window.renderSummernoteText,
                },
                {
                    data: "parent_jenis",
                    name: "parent_jp.nama",
                    className: "text-center",
                },
                {
                    data: "jenis_pengawasan",
                    name: "jenis_pengawasans.nama",
                    className: "text-center",
                },
                { data: "jadwal_rmp_bulan", name: "jadwal_rmp_bulan" },
                { data: "jadwal_rsp_bulan", name: "jadwal_rsp_bulan" },
                { data: "jadwal_rpl_bulan", name: "jadwal_rpl_bulan" },
                { data: "jadwal_hp_hari", name: "jadwal_hp_hari" },
                { data: "pj", name: "jabatans.pj", className: "text-center" },
                { data: "wpj", name: "jabatans.wpj", className: "text-center" },
                { data: "pt", name: "jabatans.pt", className: "text-center" },
                { data: "kt", name: "jabatans.kt", className: "text-center" },
                { data: "at", name: "jabatans.at", className: "text-center" },
                {
                    data: "anggaran_total",
                    name: "anggaran_total",
                    className: "text-end",
                    render: $.fn.dataTable.render.number(".", ",", 0, "Rp "),
                },
                { data: "irbanwil_nama", name: "irbanwils.nama" },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center no-export",
                    render: function (data, type, row) {
                        let buttons = "";
                        if (row.can_edit) {
                            buttons += `<a href="${row.edit_url}" class="btn btn-sm btn-warning rounded-4" title="Edit"><i class="bi bi-pencil-square"></i></a>`;
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
                                <button type="button" class="btn btn-sm btn-danger rounded-4 btn-delete" title="Hapus">
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
        });
    }

    /**
     * -------------------------------
     * Form Create/Edit
     * -------------------------------
     */
    initForm() {
        axios
            .get(routes.getAnggaran())
            .then((res) => {
                this.anggaranPerHari = res.data.anggaran;
                this.hitungSemuaAnggaran();
            })
            .catch(() => {
                this.anggaranPerHari = 170000;
            });

        // Event ketika jumlah atau jadwal_hp_hari berubah
        document.addEventListener("input", (e) => {
            if (
                e.target.matches('[name="jadwal_hp_hari"]') ||
                e.target.matches(".jumlah")
            ) {
                this.hitungSemuaAnggaran();
            }
        });
    }

    /**
     * Hitung setiap anggaran jabatan dan total keseluruhan
     */
    hitungSemuaAnggaran() {
        const hpInput = document.querySelector('[name="jadwal_hp_hari"]');
        const hpHari = parseInt(hpInput?.value || 0);
        let total = 0;

        document.querySelectorAll("#jabatanTable tbody tr").forEach((row) => {
            const jumlahInput = row.querySelector(".jumlah");
            const anggaranInput = row.querySelector(".anggaran");

            const jumlah = parseInt(jumlahInput?.value || 0);
            const anggaran = hpHari * jumlah * this.anggaranPerHari;

            // set nilai anggaran
            const an = AutoNumeric.getAutoNumericElement(anggaranInput);
            if (an) an.set(anggaran);
            else anggaranInput.value = anggaran;

            total += anggaran;
        });

        // tampilkan total di bawah
        const totalInput = document.getElementById("totalAnggaran");
        const totalAN = AutoNumeric.getAutoNumericElement(totalInput);
        if (totalAN) totalAN.set(total);
        else totalInput.value = total;
    }
}

export default new NonPkptPage();
