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
            const response = await fetch("/groupproject/Version-033/router/router.php?action=submitNewMedia", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                messageBox.textContent = "Your request has been submitted!";
                messageBox.className = "success-message";
                form.reset();
            } else {
                messageBox.textContent = data.error || "An error occurred.";
                messageBox.className = "error-message";
            }
        } catch (err) {
            console.error(err);
            messageBox.textContent = "Network error occurred.";
            messageBox.className = "error-message";
        }
    });
});
