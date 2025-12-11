// ====================================
// Write a Review Form Handling
// ====================================

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("write-review-form");
    const messageBox = document.getElementById("review-form-message");
    const reviewText = document.getElementById("review_text");

    if (!form) return;

    // Remove "(Optional)" hint when user types
    reviewText.addEventListener("input", () => {
        const optional = document.getElementById("review-optional");
        if (optional) optional.style.display = "none";
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        messageBox.innerHTML = "";

        const formData = new FormData(form);

        try {
            const response = await fetch("router.php?action=submitReview", {
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
                messageBox.textContent =
                    json.message || "Error submitting review.";
            }
        } catch (err) {
            messageBox.className = "error-message";
            messageBox.textContent = "Unexpected error. Please try again.";
        }
    });
});
