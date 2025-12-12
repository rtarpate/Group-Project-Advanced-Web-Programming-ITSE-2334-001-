// ===========================================
// form-write-review.js - Hosting-Ready Version
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
        statusMsg.style.color = "gold";

        const formData = new FormData(form);

        try {
            const response = await fetch("/router/router.php?action=submitReview", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const result = await response.json();
            console.log("form-write-review.js: response:", result);

            if (result.success) {
                statusMsg.textContent = "Your review has been submitted!";
                statusMsg.style.color = "lightgreen";
                form.reset();
            } else {
                statusMsg.textContent = result.error || "An unexpected error occurred.";
                statusMsg.style.color = "red";
            }

        } catch (err) {
            console.error("form-write-review.js error:", err);
            statusMsg.textContent = "A network or server error occurred.";
            statusMsg.style.color = "red";
        }
    });
});
