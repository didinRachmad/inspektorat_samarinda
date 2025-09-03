// Plugins & CSS
import select2 from "select2";
import "datatables.net-bs5";
import "datatables.net-buttons-bs5";
import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.print.js";
import "datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css";
import Pace from "pace-js/pace.min";
import PerfectScrollbar from "perfect-scrollbar";
import "simplebar";
import { initAutoNumeric } from "@/utils/autoNumeric";
import "./main";

// SweetAlert utilities
import {
    showAlert,
    showConfirmDialog,
    showInputDialog,
    showToast,
} from "./modules/sweetalert.js";

// ==================== CLASS DEFINITION ====================

class DashboardApp {
    constructor() {
        // dynamic page modules (Vite glob)
        this.pageModules = import.meta.glob("./pages/**/*.js");
        this.searchScrollbar = null;
    }

    // ——— 1) Fase Global Setup (langsung dieksekusi) ———
    setupGlobals() {
        select2(); // init Select2
        Pace.start(); // loading indicator

        window.showAlert = showAlert;
        window.showConfirmDialog = showConfirmDialog;
        window.showInputDialog = showInputDialog;
        window.showToast = showToast;

        window.API_URLS = {
            items: import.meta.env.VITE_API_ITEMS_SERVICE,
        };
    }

    // ——— 2) Fase Inisialisasi Ketika DOM Siap ———
    initPageScripts() {
        this.initTooltips();
        this.bindConfirmationHandler();
        this.initSearch();
        initAutoNumeric();
        this.loadPageModule();
    }

    // ——— bootstrap tooltips ———
    initTooltips() {
        document
            .querySelectorAll('[data-bs-toggle="tooltip"]')
            .forEach((el) => new bootstrap.Tooltip(el));
    }

    // ——— confirm / input dialogs for buttons ———
    bindConfirmationHandler() {
        document.addEventListener("click", (e) => {
            const btn = e.target.closest("button");
            if (!btn) return;
            const form = btn.closest("form");
            if (!form) return;

            const configs = [
                [
                    "btn-submit",
                    "Konfirmasi Penyimpanan",
                    "Periksa kembali inputan anda sebelum menyimpan!",
                    "submit",
                ],
                [
                    "btn-delete",
                    "Konfirmasi Penghapusan",
                    "Data yang dihapus tidak dapat dikembalikan!",
                    "delete",
                ],
                [
                    "btn-approve",
                    "Apakah Anda yakin?",
                    "Harap periksa kembali sebelum melakukan approve data!",
                    "submit",
                ],
                [
                    "btn-reset-password",
                    "Apakah Anda yakin?",
                    "Password akan direset ke data awal!",
                    "default",
                ],
            ];

            for (const [cls, title, text, icon] of configs) {
                if (btn.classList.contains(cls)) {
                    e.preventDefault();
                    return showConfirmDialog(
                        title,
                        text,
                        () => this._triggerFormSubmit(form),
                        icon
                    );
                }
            }

            // khusus revisi/reject
            if (
                btn.classList.contains("btn-revisi") ||
                btn.classList.contains("btn-reject")
            ) {
                e.preventDefault();
                const isRevisi = btn.classList.contains("btn-revisi");
                const txt = isRevisi
                    ? "Data item akan dikembalikan untuk proses revisi, silakan tambahkan keterangan!"
                    : "Data item akan direject! Silakan tambahkan alasan reject.";

                return showInputDialog(
                    "Apakah Anda yakin?",
                    txt,
                    (keterangan) => {
                        form.insertAdjacentHTML(
                            "beforeend",
                            `<input type="hidden" name="keterangan" value="${keterangan}">`
                        );
                        // lagi–lagi wrap agar form terdeteksi
                        this._triggerFormSubmit(form);
                    }
                );
            }
        });
    }

    // helper untuk submit form via temporary button
    _triggerFormSubmit(form) {
        const temp = document.createElement("button");
        temp.type = "submit";
        temp.style.display = "none";
        form.appendChild(temp);
        temp.click();
        temp.remove();
    }

    // ——— dynamic page loader ———
    async loadPageModule() {
        const page = document.body.dataset.page;
        const action = document.body.dataset.action;
        if (!page) return;

        const path = `./pages/${page}.js`;
        const loader = this.pageModules[path];
        if (!loader) {
            console.error(`Module untuk halaman "${page}" tidak ditemukan`);
            return;
        }

        try {
            const mod = await loader();
            const fnName = `init${
                action?.charAt(0)?.toUpperCase() + action?.slice(1) || "Index"
            }`;
            if (typeof mod.default[fnName] === "function") {
                mod.default[fnName]();
            } else {
                console.error(
                    `Fungsi "${fnName}" tidak ditemukan di module ${page}`
                );
            }
        } catch (err) {
            console.error(`Gagal memuat module halaman "${page}"`, err);
        }
    }

    // ——— search popup & results ———
    initSearch() {
        const inputSelectors = "#search-input, #mobile-search-input";
        const inputs = document.querySelectorAll(inputSelectors);
        const popup = document.querySelector(".search-popup");
        const resultsEl = document.getElementById("search-results");
        const seeAll = document.getElementById("see-all-results");

        // setup PerfectScrollbar
        this.searchScrollbar = new PerfectScrollbar(".search-content", {
            wheelSpeed: 1,
            wheelPropagation: false,
            minScrollbarLength: 20,
        });

        const debounce = (fn, delay = 300) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fn(...args), delay);
            };
        };

        const performSearch = async (query) => {
            if (query.length < 2) {
                resultsEl.innerHTML = "";
                return popup.classList.add("d-none");
            }
            popup.classList.remove("d-none");
            resultsEl.innerHTML = DashboardApp._loadingTemplate();

            try {
                const resp = await fetch(
                    `/search?q=${encodeURIComponent(query)}`,
                    {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                    }
                );
                if (!resp.ok) throw new Error(resp.status);
                const data = await resp.json();
                this._renderSearchResults(data, resultsEl, seeAll, query);
            } catch {
                resultsEl.innerHTML = `<div class="alert alert-danger m-3">Gagal memuat hasil pencarian. Silakan coba lagi.</div>`;
            }
        };

        inputs.forEach((inp) => {
            const onInput = debounce((e) =>
                performSearch(e.target.value.trim())
            );
            inp.addEventListener("input", onInput);
            inp.addEventListener("focus", () => {
                if (inp.value.length > 1) popup.classList.remove("d-none");
            });
        });

        document
            .querySelectorAll(".search-close, .mobile-search-close")
            .forEach((btn) =>
                btn.addEventListener("click", () => {
                    popup.classList.add("d-none");
                    inputs.forEach((i) => (i.value = ""));
                })
            );

        document.addEventListener("click", (e) => {
            if (!e.target.closest(".search-bar")) popup.classList.add("d-none");
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") popup.classList.add("d-none");
        });
    }

    // loading spinner markup
    static _loadingTemplate() {
        return `
      <div class="text-center p-3">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Mencari...</p>
      </div>`;
    }

    // render hasil pencarian
    _renderSearchResults(data, container, seeAllBtn, query) {
        let html = "",
            total = 0;
        const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);
        const formatTitle = (key) => key.split("_").map(capitalize).join(" ");

        if (data.error) {
            html = `<div class="alert alert-danger m-3">${data.error}</div>`;
        } else {
            for (const [key, items] of Object.entries(data)) {
                if (!items?.length) continue;
                total += items.length;
                html += `<p class="search-title">${formatTitle(
                    key
                )}</p><div class="search-list d-flex flex-column gap-2">`;
                for (const { url = "#", icon = "search", display } of items) {
                    html += `
            <a href="${url}" class="search-list-item d-flex align-items-center gap-3">
              <div class="list-icon"><i class="material-icons-outlined fs-5">${icon}</i></div>
              <div class="text-light">${display}</div>
            </a>`;
                }
                html += `</div><hr>`;
                this.searchScrollbar.update();
            }
            if (!html) {
                html =
                    '<p class="text-muted m-3">Tidak ditemukan hasil pencarian</p>';
            }
        }

        if (query && total) {
            seeAllBtn.href = `/search/all?q=${encodeURIComponent(query)}`;
            seeAllBtn.classList.remove("d-none");
            seeAllBtn.innerHTML = `<i class="material-icons-outlined me-2">search</i> Lihat Semua Hasil (${total}+)`;
        } else {
            seeAllBtn.classList.add("d-none");
        }

        container.innerHTML = html;
    }
}

// ==================== BOOTSTRAP APP ====================
const app = new DashboardApp();
app.setupGlobals();

// ——— BOOTSTRAP: Inisialisasi sisanya setelah DOM siap ———
document.addEventListener("DOMContentLoaded", () => {
    app.initPageScripts();
});
