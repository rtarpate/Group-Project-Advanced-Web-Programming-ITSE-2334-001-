
// writeanewreview.js - Vanilla JS for Write a New Review form functionality

// Wait for DOM to load
document.addEventListener("DOMContentLoaded", function() {
    const reviewForm = document.getElementById("new-review-form");
    const mediaSelect = document.getElementById("media-title");
    const mediaTypeField = document.getElementById("media-type");
    const ratingInput = document.getElementById("rating");
    const reviewText = document.getElementById("review-text");

    // Load sample data from reviews.html (simulated)
    const sampleMedia = [
        { title: "The Grand Adventure", type: "Movie" },
        { title: "Galaxy Quest 2", type: "Movie" },
        { title: "Pixel Warzone", type: "Video Game" },
        { title: "Echoes of Eternity", type: "TV" }
    ];

    // Populate media titles dropdown
    if (mediaSelect) {
        sampleMedia.forEach(item => {
            const option = document.createElement("option");
            option.value = item.title;
            option.textContent = item.title;
            mediaSelect.appendChild(option);
        });
    }

    // Auto-update media type when a title is selected
    if (mediaSelect && mediaTypeField) {
        mediaSelect.addEventListener("change", () => {
            const selected = sampleMedia.find(m => m.title === mediaSelect.value);
            mediaTypeField.value = selected ? selected.type : "";
        });
    }

    // Form submission handler
    if (reviewForm) {
        reviewForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const mediaTitle = mediaSelect.value.trim();
            const mediaType = mediaTypeField.value.trim();
            const rating = parseInt(ratingInput.value);
            const review = reviewText.value.trim();

            // Validation
            if (!mediaTitle) return alert("Please select a media title.");
            if (!mediaType) return alert("Media type could not be determined.");
            if (isNaN(rating) || rating < 1 || rating > 10) return alert("Please enter a rating between 1 and 10.");
            if (review.length < 10) return alert("Please enter a review of at least 10 characters.");

            // Sanitize text
            const sanitize = str => str.replace(/[<>]/g, "").trim();

            const newReview = {
                title: sanitize(mediaTitle),
                type: sanitize(mediaType),
                rating: rating,
                review: sanitize(review)
            };

            // Temporarily store review (in-memory only for this session)
            sessionStorage.setItem("tempReview", JSON.stringify(newReview));

            // Redirect to reviews.html to view added data
            window.location.href = "reviews.html";
        });
    }

    // If on reviews.html, display the temporary review
    if (window.location.pathname.includes("reviews.html")) {
        const reviewsContainer = document.getElementById("reviews-container");
        const tempReview = sessionStorage.getItem("tempReview");

        if (reviewsContainer && tempReview) {
            const review = JSON.parse(tempReview);
            const reviewCard = document.createElement("div");
            reviewCard.className = "review-card";
            reviewCard.innerHTML = `
                <h3>${review.title}</h3>
                <p><strong>Type:</strong> ${review.type}</p>
                <p><strong>Rating:</strong> ${review.rating}/10</p>
                <p>${review.review}</p>
                <hr>
            `;
            reviewsContainer.appendChild(reviewCard);
        }
    }
});
