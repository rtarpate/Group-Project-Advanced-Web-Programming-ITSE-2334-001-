// form-write-review.js
document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("writeReviewForm");
    const statusMsg = document.getElementById("reviewStatus");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        statusMsg.textContent = "Submitting...";
        statusMsg.style.color = "gold";

        const formData = new FormData(form);

        try {
            const response = await fetch(
                "../router/router.php?action=submitReview",
                {
                    method: "POST",
                    body: formData
                }
            );

            if (!response.ok) {
                throw new Error("Network response was not ok");
            }

            const result = await response.json();

            if (result.success) {
                statusMsg.textContent = "Review submitted successfully!";
                statusMsg.style.color = "green";
                form.reset();
            } else {
                statusMsg.textContent = result.error || "An unexpected error occurred.";
                statusMsg.style.color = "red";
            }

        } catch (err) {
            console.error(err);
            statusMsg.textContent = "A network error occurred.";
            statusMsg.style.color = "red";
        }
    });

});
