import route from "@/routes";

const routes = {
    store: () => route("faq.store"),
    update: (id) => route("faq.update", { faq: id }),
};

class FaqPage {
    constructor() {
        this.pageName = "Master FAQ";

        // Modal & form
        this.modalEl = document.getElementById("faqModal");
        this.modal = new bootstrap.Modal(this.modalEl);
        this.formEl = document.getElementById("faqForm");
        this.faqIdInput = document.getElementById("faq_id");
        this.questionInput = document.getElementById("question");
        this.answerInput = document.getElementById("answer");

        // Tombol tambah
        const btnTambah = document.getElementById("btnTambahFaq");
        if (btnTambah) {
            btnTambah.addEventListener("click", () => this.openAddModal());
        }

        // Tombol edit FAQ
        document.addEventListener("click", (e) => {
            if (e.target.closest(".btn-edit-faq")) {
                const btn = e.target.closest(".btn-edit-faq");
                this.openEditModal({
                    id: btn.dataset.id,
                    question: btn.dataset.question,
                    answer: btn.dataset.answer,
                });
            }
        });
    }

    initIndex() {
        console.log(`Halaman ${this.pageName} berhasil dimuat!`);
        this.initFormSubmit();
    }

    openAddModal() {
        this.formEl.reset();
        this.faqIdInput.value = "";
        $("#answer").summernote("code", ""); // kosongkan editor
        this.modalEl.querySelector(".modal-title").textContent = "Tambah FAQ";
        this.modal.show();
    }

    openEditModal(data) {
        this.faqIdInput.value = data.id;
        this.questionInput.value = data.question;
        $("#answer").summernote("code", data.answer); // set konten Summernote
        this.modalEl.querySelector(".modal-title").textContent = "Edit FAQ";
        this.modal.show();
    }

    // Submit form via AJAX
    initFormSubmit() {
        this.formEl.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(this.formEl);
            formData.set("answer", $("#answer").summernote("code"));
            const id = this.faqIdInput.value;
            let url = id ? routes.update(id) : routes.store();
            if (id) formData.append("_method", "PUT");

            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: (res) => {
                    this.modal.hide();
                    // Reload halaman agar accordion update otomatis
                    location.reload();
                    showToast(
                        res.message || "FAQ berhasil disimpan.",
                        "success"
                    );
                },
                error: (err) => {
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

const faqPage = new FaqPage();
window.FaqPage = faqPage;
export default faqPage;
