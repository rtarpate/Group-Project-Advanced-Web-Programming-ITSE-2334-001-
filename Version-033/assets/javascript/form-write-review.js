// ================================================
// form-write-review.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const form       = document.getElementById("write-review-form");
    const messageBox = document.getElementById("review-form-message");
    const mediaSelect = document.getElementById("media_id");

    if (!form || !mediaSelect) return;

    // Load media into dropdown
    fetch("/groupproject/Version-033/router/router.php?action=getMedia")
        .then((response) => response.json())
        .then((data) => {
            if (!data.success || !Array.isArray(data.media)) {
                throw new Error("Invalid response from server.");
            }

            data.media.forEach((item) => {
                const opt = document.createElement("option");
                opt.value = item.media_id;
                opt.textContent = `${item.media_title} (${item.media_type})`;
                mediaSelect.appendChild(opt);
            });
        })
        .catch((err) => {
            console.error(err);
            messageBox.textContent = "Failed to load media list.";
            messageBox.className = "error-message";
        });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        messageBox.textContent = "";

        const formData = new FormData(form);

        try {
            const response = await fetch("/groupproject/Version-033/router/router.php?action=submitReview", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                messageBox.textContent = "Review submitted successfully!";
                messageBox.className = "success-message";
                form.reset();
            } else {
                messageBox.textContent = data.error || "Failed to submit review.";
                messageBox.className = "error-message";
            }
        } catch (err) {
            console.error(err);
            messageBox.textContent = "Network error occurred.";
            messageBox.className = "error-message";
        }
    });
});
