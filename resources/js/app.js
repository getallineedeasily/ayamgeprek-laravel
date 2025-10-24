import "./bootstrap";
import "../css/app.css";

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("form").forEach((form) => {
        form.addEventListener("submit", (e) => {
            const btn = form.querySelector('[type="submit"]');
            const loading = form.querySelector(".loading");
            if (btn) {
                btn.disabled = true;
                btn.style.display = "none";
            }
            if (loading) loading.style.display = "block";
        });
    });
});
