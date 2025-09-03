import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/landing.css",
                "resources/js/landing.js",
                "resources/css/auth.css",
                "resources/js/auth.js",
                "resources/css/dashboard.css",
                "resources/js/dashboard.js",
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: "manifest.json",
        outDir: "public/build",
        emptyOutDir: true,
        rollupOptions: {
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/landing.css",
                "resources/js/landing.js",
                "resources/css/auth.css",
                "resources/js/auth.js",
                "resources/css/dashboard.css",
                "resources/js/dashboard.js",
            ],
            output: {
                manualChunks: {
                    vendor: [
                        "jquery",
                        "select2",
                        "bootstrap",
                        "datatables.net-bs5",
                        "sweetalert2",
                    ],
                },
            },
        },
    },
});
