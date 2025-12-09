// File: public/assets/javascript/FormHandling.js

document.addEventListener("DOMContentLoaded", () => {
  /* =========================
     COMMON HELPER FUNCTIONS
  ========================= */
  function showError(input, message) {
    let error = input.nextElementSibling;
    if (!error || !error.classList.contains("error-message")) {
      error = document.createElement("p");
      error.className = "error-message";
      input.insertAdjacentElement("afterend", error);
    }
    error.textContent = message;
  }

  function clearError(input) {
    const error = input.nextElementSibling;
    if (error && error.classList.contains("error-message")) {
      error.remove();
    }
  }

  function createSuccessMessage(form, text = "✅ Form submitted successfully!") {
    let msg = form.querySelector(".success-message");
    if (!msg) {
      msg = document.createElement("p");
      msg.className = "success-message";
      msg.textContent = text;
      form.appendChild(msg);
    }
    msg.style.display = "block";
    msg.style.opacity = "1";

    // Auto-hide after 3 seconds
    setTimeout(() => {
      msg.style.transition = "opacity 0.5s ease";
      msg.style.opacity = "0";
      setTimeout(() => {
        msg.style.display = "none";
      }, 500);
    }, 3000);
  }

  /* =========================
     REQUEST NEW MEDIA FORM
  ========================= */
  const requestForm = document.getElementById("request-media-form");
  if (requestForm) {
    requestForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const nameField = document.getElementById("media-name");
      const typeField = document.getElementById("media-type");
      const descField = document.getElementById("media-description");

      let isValid = true;

      if (nameField.value.trim() === "") {
        showError(nameField, "Please enter a media name.");
        isValid = false;
      } else clearError(nameField);

      if (typeField.value === "") {
        showError(typeField, "Please select a media type.");
        isValid = false;
      } else clearError(typeField);

      if (descField.value.trim() === "") {
        showError(descField, "Please enter a short description.");
        isValid = false;
      } else clearError(descField);

      if (isValid) {
        requestForm.reset();
        createSuccessMessage(requestForm, "✅ Your request has been submitted successfully!");
      }
    });

    // Remove success message on new input
    requestForm.addEventListener("input", () => {
      const msg = requestForm.querySelector(".success-message");
      if (msg) msg.style.display = "none";
    });
  }

  /* =========================
     WRITE REVIEW FORM
  ========================= */
  const reviewForm = document.getElementById("new-review-form");
  if (reviewForm) {
    reviewForm.addEventListener("submit", (event) => {
      event.preventDefault();

      const titleField = document.getElementById("media-title");
      const ratingField = document.getElementById("rating");

      let isValid = true;

      // Validate media title
      if (titleField.value === "" || titleField.value === null) {
        showError(titleField, "Please select a media title.");
        isValid = false;
      } else clearError(titleField);

      // Validate rating
      const ratingValue = parseFloat(ratingField.value);
      if (isNaN(ratingValue) || ratingValue < 1 || ratingValue > 10) {
        showError(ratingField, "Please enter a rating between 1 and 10.");
        isValid = false;
      } else clearError(ratingField);

      if (isValid) {
        reviewForm.reset();
        createSuccessMessage(reviewForm, "✅ Review submitted successfully!");
      }
    });

    // Remove success message on new input
    reviewForm.addEventListener("input", () => {
      const msg = reviewForm.querySelector(".success-message");
      if (msg) msg.style.display = "none";
    });
  }
});
