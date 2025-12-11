// ================================================
// form-request-new-media.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("request-new-media-form");
    const messageBox = document.getElementById("form-message");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        messageBox.innerHTML = "";

        const formData = new FormData(form);

        try {
            const response = await fetch("pages/router.php?action=submitNewMedia", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                messageBox.className = "success-message";
                messageBox.textContent = "New media request submitted successfully!";
                form.reset();
            } else {
                messageBox.className = "error-message";
                messageBox.textContent = data.error || "An unexpected error occurred.";
            }
        } catch (err) {
            messageBox.className = "error-message";
            messageBox.textContent = "Network error. Please try again.";
        }
    });
});
