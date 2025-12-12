// ===========================================
// form-write-review.js - Hosting-Ready Version
// Handles AJAX submission of the write review form
// ===========================================

document.addEventListener("DOMContentLoaded", () => {
    const form      = document.getElementById("writeReviewForm");
    const statusMsg = document.getElementById("reviewStatus");

    if (!form || !statusMsg) {
        console.warn("form-write-review.js: form or status element not found.");
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        statusMsg.textContent = "Submitting your review...";
        statusMsg.style.color = "#333";

        const formData = new FormData(form);

        try {
            const response = await fetch("/router/router.php?action=submitReview", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error("HTTP status " + response.status);
            }

            const result = await response.json();

            if (result.success) {
                statusMsg.textContent = result.message || "Review submitted successfully.";
                statusMsg.style.color = "green";
                form.reset();
            } else {
                statusMsg.textContent = result.error || "An unexpected error occurred.";
                statusMsg.style.color = "red";
            }

        } catch (err) {
            console.error("form-write-review.js error:", err);
            statusMsg.textContent = "A network or server error occurred. Please try again later.";
            statusMsg.style.color = "red";
        }
    });
});
