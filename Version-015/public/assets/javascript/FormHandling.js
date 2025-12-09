// File: public/assets/javascript/FormHandling.js

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("request-media-form");

  // Create message elements
  const successMessage = document.createElement("p");
  successMessage.textContent = "✅ Your request has been submitted successfully!";
  successMessage.style.color = "green";
  successMessage.style.display = "none";
  form.appendChild(successMessage);

  // Function to show error below an input
  function showError(input, message) {
    let error = input.nextElementSibling;
    if (!error || !error.classList.contains("error-message")) {
      error = document.createElement("p");
      error.className = "error-message";
      error.style.color = "red";
      error.style.fontSize = "0.9em";
      error.style.margin = "4px 0 0 0";
      input.insertAdjacentElement("afterend", error);
    }
    error.textContent = message;
  }

  // Function to clear error messages
  function clearError(input) {
    const error = input.nextElementSibling;
    if (error && error.classList.contains("error-message")) {
      error.remove();
    }
  }

  // Handle form submission
  form.addEventListener("submit", (event) => {
    event.preventDefault(); // Stop form from reloading page
    successMessage.style.display = "none";

    let isValid = true;

    // Get fields
    const nameField = document.getElementById("media-name");
    const typeField = document.getElementById("media-type");
    const descField = document.getElementById("media-description");

    // Validate name
    if (nameField.value.trim() === "") {
      showError(nameField, "Please enter a media name.");
      isValid = false;
    } else {
      clearError(nameField);
    }

    // Validate type
    if (typeField.value === "") {
      showError(typeField, "Please select a media type.");
      isValid = false;
    } else {
      clearError(typeField);
    }

    // Validate description
    if (descField.value.trim() === "") {
      showError(descField, "Please enter a short description.");
      isValid = false;
    } else {
      clearError(descField);
    }

    // If all valid
    if (isValid) {
      successMessage.style.display = "block";
      form.reset();
    }
  });
});
