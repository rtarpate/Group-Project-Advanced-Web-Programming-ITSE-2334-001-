// ================================
// Request New Media Form Handling
// ================================

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("new-media-form");
    const messageBox = document.getElementById("form-message");

    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        messageBox.innerHTML = "";

        const formData = new FormData(form);

        try {
            const response = await fetch("router.php?action=submitNewMedia", {
                method: "POST",
                body: formData,
            });

            const json = await response.json();

            if (json.status === "success") {
                messageBox.className = "success-message";
                messageBox.textContent = json.message;

                form.reset();

                setTimeout(() => {
                    messageBox.textContent = "";
                }, 3000);
            } else {
                messageBox.className = "error-message";
                messageBox.textContent = json.message || "Error submitting form.";
            }
        } catch (err) {
            messageBox.className = "error-message";
            messageBox.textContent = "Unexpected error. Please try again.";
        }
    });
});
