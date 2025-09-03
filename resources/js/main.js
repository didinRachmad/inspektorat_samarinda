import $ from "jquery";
import "metismenu/dist/metisMenu.min.js";
import PerfectScrollbar from "perfect-scrollbar";

$(function () {
    "use strict";

    // document.addEventListener("DOMContentLoaded", function () {
    //     if (document.querySelector(".search-content")) {
    //         new PerfectScrollbar(".search-content");
    //     }
    // });

    /* toggle button */
    $(".btn-toggle").click(function () {
        $("body").hasClass("toggled")
            ? ($("body").removeClass("toggled"),
              $(".sidebar-wrapper").unbind("hover"))
            : ($("body").addClass("toggled"),
              $(".sidebar-wrapper").hover(
                  function () {
                      $("body").addClass("sidebar-hovered");
                  },
                  function () {
                      $("body").removeClass("sidebar-hovered");
                  }
              ));
    });

    /* menu */

    $(function () {
        $("#sidenav").metisMenu();
    });

    $(".sidebar-close").on("click", function () {
        $("body").removeClass("toggled");
    });

    // /* dark mode button */

    // $(".dark-mode i").click(function () {
    //     $(this).text(function (i, v) {
    //         return v === "dark_mode" ? "light_mode" : "dark_mode";
    //     });
    // });

    // $(".dark-mode").click(function () {
    //     $("html").attr("data-bs-theme", function (i, v) {
    //         return v === "dark" ? "light" : "dark";
    //     });
    // });

    /* sticky header */

    $(document).ready(function () {
        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 40) {
                $(".top-header .navbar").addClass("sticky-header");
            } else {
                $(".top-header .navbar").removeClass("sticky-header");
            }
        });
    });

    /* email */

    // $(".email-toggle-btn").on("click", function () {
    //     $(".email-wrapper").toggleClass("email-toggled");
    // });
    // $(".email-toggle-btn-mobile").on("click", function () {
    //     $(".email-wrapper").removeClass("email-toggled");
    // });
    // $(".compose-mail-btn").on("click", function () {
    //     $(".compose-mail-popup").show();
    // });
    // $(".compose-mail-close").on("click", function () {
    //     $(".compose-mail-popup").hide();
    // });

    // /* chat */

    // $(".chat-toggle-btn").on("click", function () {
    //     $(".chat-wrapper").toggleClass("chat-toggled");
    // });
    // $(".chat-toggle-btn-mobile").on("click", function () {
    //     $(".chat-wrapper").removeClass("chat-toggled");
    // });

    /* switcher */
    function setTheme(themeId) {
        const themeMap = {
            BlueTheme: "blue-theme",
            LightTheme: "light",
            DarkTheme: "dark",
            SemiDarkTheme: "semi-dark",
            BoderedTheme: "bodered-theme",
        };

        // Terapkan tema ke HTML
        const themeName = themeMap[themeId];
        $("html").attr("data-bs-theme", themeName);

        // Simpan ke localStorage
        localStorage.setItem("selectedTheme", themeId);

        // Perbarui switcher
        updateThemeSwitcher(themeId);
    }

    // Fungsi untuk memperbarui tampilan switcher
    function updateThemeSwitcher(themeId) {
        $('input[name="theme-options"]').prop("checked", false);
        $("#" + themeId).prop("checked", true);
    }

    // Event handler untuk tombol tema
    $("#BlueTheme").on("click", function () {
        setTheme("BlueTheme");
    });

    $("#LightTheme").on("click", function () {
        setTheme("LightTheme");
    });

    $("#DarkTheme").on("click", function () {
        setTheme("DarkTheme");
    });

    $("#SemiDarkTheme").on("click", function () {
        setTheme("SemiDarkTheme");
    });

    $("#BoderedTheme").on("click", function () {
        setTheme("BoderedTheme");
    });

    // Muat tema yang disimpan saat halaman dimuat
    function loadTheme() {
        const savedTheme = localStorage.getItem("selectedTheme") || "BlueTheme";
        setTheme(savedTheme);
    }

    // Panggil saat halaman dimuat
    loadTheme();

    /* search control */

    $(".search-control").click(function () {
        $(".search-popup").addClass("d-block");
        $(".search-close").addClass("d-block");
    });

    $(".search-close").click(function () {
        $(".search-popup").removeClass("d-block");
        $(".search-close").removeClass("d-block");
    });

    $(".mobile-search-btn").click(function () {
        $(".search-popup").addClass("d-block");
    });

    $(".mobile-search-close").click(function () {
        $(".search-popup").removeClass("d-block");
    });

    /* menu active */

    $(function () {
        // Ambil segment pertama dari URL
        const currentSegment = window.location.pathname.split("/")[1];

        // Loop semua link dan cocokan segment awalnya
        let o = $(".metismenu li a")
            .filter(function () {
                const linkSegment = new URL(this.href).pathname.split("/")[1];
                return linkSegment === currentSegment;
            })
            .parent()
            .addClass("mm-active");

        // Buka parent menu jika nested
        while (o.is("li")) {
            o = o.parent().addClass("mm-show").parent().addClass("mm-active");
        }
    });
});
