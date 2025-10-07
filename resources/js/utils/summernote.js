// resources/js/utils/summernote.js
window.renderSummernoteText = function (data, type, row) {
    if (!data) return "";

    // buat elemen sementara untuk parsing HTML
    const tempDiv = document.createElement("div");
    tempDiv.innerHTML = data;

    // hapus seluruh inline style dan tag yang tidak penting
    tempDiv.querySelectorAll("*").forEach((el) => el.removeAttribute("style"));

    const formatted = tempDiv.innerHTML
        .replace(/<p>/g, "<div class='mb-1'>")
        .replace(/<\/p>/g, "</div>")
        .replace(/<ul>/g, "<ul class='mb-1 ps-3'>")
        .replace(/<ol>/g, "<ol class='mb-1 ps-3'>");

    return formatted;
};
