// ===================================================
// form-request-new-media.js - UX helper (fade message)
// ===================================================

document.addEventListener("DOMContentLoaded", () => {
    const msg = document.getElementById("msg");
    if (!msg) {
        console.warn("form-request-new-media.js: #msg not found.");
        return;
    }

    const text = msg.textContent.trim();
    if (text === "") return;

    msg.style.display = "block";

    // Fade out after a short delay
    setTimeout(() => {
        msg.classList.add("fade-out");
    }, 1200);
});
