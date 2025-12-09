// Version-013 writeanewreview.js
// Uses sessionStorage to temporarily replace a review by title with user input
// without altering permanent sample HTML.

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("new-review-form");
  const ratingInput = document.getElementById("rating");
  const reviewText = document.getElementById("review-text");
  const mediaTitleInput = document.getElementById("title") || document.getElementById("media-title");

  // Sanitize helper
  const sanitize = (str) => str ? str.replace(/[<>]/g, "").trim() : "";

  // Convert numeric rating to star string (1-10)
  function toStars(rating) {
    rating = Math.max(1, Math.min(10, rating));
    const filled = "★".repeat(rating);
    const empty = "☆".repeat(10 - rating);
    return filled + empty;
  }

  // When form is submitted, save review in sessionStorage
  if (form) {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const title = sanitize(mediaTitleInput.value);
      const rating = parseInt(ratingInput.value);
      const review = sanitize(reviewText.value);

      if (!title) return;
      if (isNaN(rating) || rating < 1 || rating > 10) return;
      if (!review) return;

      const newReview = { title, rating, review };
      sessionStorage.setItem("tempReview", JSON.stringify(newReview));
    });
  }

  // On reviews.html: replace matching review
  if (window.location.pathname.includes("reviews.html")) {
    const stored = sessionStorage.getItem("tempReview");
    if (!stored) return;

    try {
      const data = JSON.parse(stored);
      const titleMatch = data.title.toLowerCase().trim();
      const reviews = document.querySelectorAll(".review");

      reviews.forEach((rev) => {
        const h3 = rev.querySelector("h3");
        if (!h3) return;
        const text = h3.textContent.toLowerCase().trim();
        if (text === titleMatch) {
          const starP = rev.querySelector(".star");
          const textP = rev.querySelectorAll("p");
          if (starP) starP.textContent = toStars(data.rating);
          // Find last paragraph (the review text)
          const lastP = textP[textP.length - 1];
          if (lastP) lastP.textContent = data.review;
        }
      });
    } catch (err) {
      console.error("Error applying tempReview:", err);
    }
  }
});
