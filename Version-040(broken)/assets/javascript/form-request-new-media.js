document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("request-new-media-form");
    const messageBox = document.getElementById("form-message");

    form.addEventListener("submit", async e => {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const res = await fetch("../router/router.php?action=submitNewMedia", {
                method: "POST",
                body: formData
            });

            if (!res.ok) {
                throw new Error("Network response was not ok");
            }

            const data = await res.json();

            if (data.success) {
                messageBox.textContent = "Request submitted successfully!";
                messageBox.className = "success-message";
                form.reset();
            } else {
                messageBox.textContent = data.error || "Error submitting request.";
                messageBox.className = "error-message";
            }

        } catch (err) {
            console.error(err);
            messageBox.textContent = "Network error.";
            messageBox.className = "error-message";
        }
    });
});
