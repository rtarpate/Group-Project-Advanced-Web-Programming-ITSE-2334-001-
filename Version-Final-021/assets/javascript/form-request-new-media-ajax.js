document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("requestMediaForm");
  const msg  = document.getElementById("requestStatus");

  if (!form || !msg) return;

  function show(text, ok) {
    msg.textContent = text;
    msg.style.display = "block";
    msg.className = ok ? "success-message" : "error-message";
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    msg.style.display = "none";

    const formData = new FormData(form);

    try {
      const res = await fetch("/router/router.php?action=requestMedia", {
        method: "POST",
        body: formData
      });

      if (!res.ok) throw new Error("HTTP " + res.status);

      const data = await res.json();

      if (data.success) {
        show(data.message || "Request submitted successfully.", true);
        form.reset();
        const sel = form.querySelector("select[name='media_type']");
        if (sel && sel.options.length > 0) sel.selectedIndex = 0;
      } else {
        show(data.message || "Unable to submit request.", false);
      }
    } catch (err) {
      console.error(err);
      show("An unexpected error occurred. Please try again.", false);
    }
  });
});
