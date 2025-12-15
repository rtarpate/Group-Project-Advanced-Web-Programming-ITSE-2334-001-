document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("requestMediaForm");
    const msg  = document.getElementById("requestStatus");

    if (!form || !msg) return;

    function showMessage(text, isSuccess) {
        msg.textContent = text;
        msg.style.display = "block";
        msg.style.opacity = "1";
        msg.className = "form-status " + (isSuccess ? "success-message" : "error-message");

        if (isSuccess) {
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

        showMessage("Submitting request...", false);

        const formData = new FormData(form);

        try {
            const res = await fetch("/router/router.php?action=requestMedia", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                showMessage(data.message || "Request submitted successfully!", true);
                form.reset();
                const sel = form.querySelector("select");
                if (sel) sel.selectedIndex = 0;
            } else {
                showMessage(data.message || "Submission failed.", false);
            }
        } catch (err) {
            showMessage("Server error. Please try again later.", false);
        }
    });
});
