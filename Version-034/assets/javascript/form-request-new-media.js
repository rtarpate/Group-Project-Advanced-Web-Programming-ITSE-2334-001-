// ================================================
// form-request-new-media.js - submit new media requests
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const form       = document.getElementById("request-new-media-form");
    const messageBox = document.getElementById("form-message");

    if (!form) {
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (messageBox) {
            messageBox.textContent = "";
            messageBox.className = "";
        }

        const formData = new FormData(form);

        try {
            const response = await fetch("/groupproject/Version-033/router/router.php?action=submitNewMedia", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                if (messageBox) {
                    messageBox.className = "success-message";
                    messageBox.textContent = "New media request submitted successfully!";
                }
                form.reset();
            } else {
                if (messageBox) {
                    messageBox.className = "error-message";
                    messageBox.textContent = data.error || "An unexpected error occurred.";
                }
            }
        } catch (err) {
            console.error(err);
            if (messageBox) {
                messageBox.className = "error-message";
                messageBox.textContent = "Network error. Please try again.";
            }
        }
    });
});
