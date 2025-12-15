document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("writeReviewForm");
    const msg  = document.getElementById("reviewStatus");

    if (!form || !msg) return;

    function showMessage(text, isSuccess) {
        msg.textContent = text;
        msg.style.display = "block";
        msg.style.opacity = "1";
        msg.className = "form-status " + (isSuccess ? "success-message" : "error-message");

        if (isSuccess) {
            // Fade out AFTER user has time to read
            setTimeout(() => {
                msg.style.transition = "opacity 0.8s ease";
                msg.style.opacity = "0";

                setTimeout(() => {
                    msg.style.display = "none";
                    msg.style.transition = "";
                }, 800);
            }, 3500);
        }
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        showMessage("Submitting your review...", false);

        const formData = new FormData(form);

        try {
            const res  = await fetch("/router/router.php?action=submitReview", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                showMessage(data.message || "Review submitted successfully!", true);

                // ⏳ IMPORTANT: delay reset so message is visible
                setTimeout(() => {
                    form.reset();
                }, 600);
            } else {
                showMessage(data.message || "Submission failed.", false);
            }

        } catch (err) {
            showMessage("Server error. Please try again later.", false);
        }
    });
});
