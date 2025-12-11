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
                "/groupproject/Version-035/router/router.php?action=submitReview",
                {
                    method: "POST",
                    body: formData
                }
            );

            const result = await response.json();

            if (result.success) {
                statusMsg.textContent = "Your review has been submitted!";
                statusMsg.style.color = "lightgreen";

                // Clear the form after success
                form.reset();

                // Fade out after 3 seconds
                setTimeout(() => {
                    statusMsg.textContent = "";
                }, 3000);

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
