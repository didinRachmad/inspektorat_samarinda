export async function fetchJson(url, options = {}) {
    const opts = {
        method: "GET",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": window.csrfToken, // Pastikan csrfToken sudah didefinisikan di window
        },
        ...options,
    };

    try {
        const res = await fetch(url, opts);

        if (!res.ok) {
            throw new Error(`Error: ${res.status} - ${res.statusText}`);
        }

        return await res.json();
    } catch (err) {
        console.error("Fetch error:", err);
        throw err;
    }
}
