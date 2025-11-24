// ================================================
// form-write-review.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("write-review-form");
    const messageBox = document.getElementById("form-message");

    if (!form) return;

    // Load media into dropdown
    fetch("pages/router.php?action=getMedia")
        .then((response) => response.json())
        .then((data) => {
            const dropdown = document.getElementById("media-title");
            data.media.forEach((item) => {
                const option = document.createElement("option");
                option.value = item.media_id;
                option.textContent = item.title;
                dropdown.appendChild(option);
            });
        });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        messageBox.innerHTML = "";

        const formData = new FormData(form);

        try {
            const response = await fetch("pages/router.php?action=submitReview", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                messageBox.className = "success-message";
                messageBox.textContent = "Review submitted successfully!";
                form.reset();
            } else {
                messageBox.className = "error-message";
                messageBox.textContent = data.error || "An unexpected error occurred.";
            }
        } catch (err) {
            messageBox.className = "error-message";
            messageBox.textContent = "Network error. Please try again.";
        }
    });
});
