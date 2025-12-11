// ================================================
// form-write-review.js - submit reviews via router
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const form        = document.getElementById("write-review-form");
    const messageBox  = document.getElementById("form-message");
    const mediaSelect = document.getElementById("media_id");

    if (!form || !mediaSelect) {
        return;
    }

    // Load media into dropdown
    fetch("/groupproject/Version-033/router/router.php?action=getMedia")
        .then(response => response.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.media)) {
                throw new Error("Invalid response from server.");
            }

            mediaSelect.innerHTML = '<option value="">-- Select Media --</option>';

            data.media.forEach(item => {
                const opt = document.createElement("option");
                opt.value = item.media_id;
                opt.textContent = `${item.media_title} (${item.media_type})`;
                mediaSelect.appendChild(opt);
            });
        })
        .catch(err => {
            console.error(err);
            if (messageBox) {
                messageBox.textContent = "Failed to load media list.";
                messageBox.className = "error-message";
            }
        });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (messageBox) {
            messageBox.textContent = "";
            messageBox.className = "";
        }

        const formData = new FormData(form);

        try {
            const response = await fetch("/groupproject/Version-033/router/router.php?action=submitReview", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                if (messageBox) {
                    messageBox.className = "success-message";
                    messageBox.textContent = "Review submitted successfully!";
                }
                form.reset();
            } else {
                if (messageBox) {
                    messageBox.className = "error-message";
                    messageBox.textContent = data.error || "Failed to submit review.";
                }
            }
        } catch (err) {
            console.error(err);
            if (messageBox) {
                messageBox.className = "error-message";
                messageBox.textContent = "Network error. Please try again.";
            }
        }
    });
});
