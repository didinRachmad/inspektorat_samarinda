document.addEventListener("DOMContentLoaded", function () {
    document
        .querySelector("#show_hide_password a")
        .addEventListener("click", function (e) {
            e.preventDefault();
            const input = document.querySelector("#show_hide_password input");
            const type =
                input.getAttribute("type") === "password" ? "text" : "password";
            input.setAttribute("type", type);

            const icon = e.currentTarget.querySelector("i");
            icon.classList.toggle("bi-eye-fill");
            icon.classList.toggle("bi-eye-slash-fill");
        });
});

