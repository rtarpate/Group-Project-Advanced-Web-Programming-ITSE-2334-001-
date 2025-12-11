
// writeanewreview.js - Vanilla JavaScript for Write a New Review form and Reviews display

document.addEventListener("DOMContentLoaded", function () {
    const reviewForm = document.getElementById("new-review-form");
    const mediaSelect = document.getElementById("media-title");
    const mediaTypeField = document.getElementById("media-type");
    const ratingInput = document.getElementById("rating");
    const reviewText = document.getElementById("review-text");

    // Parse sample data directly from reviews.html (by looking for data attributes or fallback)
    const defaultMedia = [
        { title: "The Grand Adventure", type: "Movie" },
        { title: "Galaxy Quest 2", type: "Movie" },
        { title: "Pixel Warzone", type: "Video Game" },
        { title: "Echoes of Eternity", type: "TV" }
    ];

    // Populate dropdown list
    if (mediaSelect) {
        defaultMedia.forEach(item => {
            const opt = document.createElement("option");
            opt.value = item.title;
            opt.textContent = item.title;
            mediaSelect.appendChild(opt);
        });
    }

    // Auto-update media type when a media title is selected
    if (mediaSelect && mediaTypeField) {
        mediaSelect.addEventListener("change", () => {
            const selected = defaultMedia.find(m => m.title === mediaSelect.value);
            mediaTypeField.value = selected ? selected.type : "";
        });
    }

    // Sanitize helper function
    const sanitize = str => str.replace(/[<>]/g, "").trim();

    // Handle form submission
    if (reviewForm) {
        reviewForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const title = sanitize(mediaSelect.value);
            const type = sanitize(mediaTypeField.value);
            const rating = parseInt(ratingInput.value);
            const review = sanitize(reviewText.value);

            if (!title) return alert("Please select a media title.");
            if (!type) return alert("Media type could not be determined.");
            if (isNaN(rating) || rating < 1 || rating > 10) return alert("Rating must be between 1 and 10.");
            if (review.length < 10) return alert("Please enter a review of at least 10 characters.");

            const newReview = { title, type, rating, review };

            sessionStorage.setItem("tempReview", JSON.stringify(newReview));
            window.location.href = "reviews.html";
        });
    }

    // Inject review into reviews.html dynamically
    if (window.location.pathname.includes("reviews.html")) {
        const container = document.getElementById("reviews-container");
        const stored = sessionStorage.getItem("tempReview");

        if (stored && container) {
            const r = JSON.parse(stored);
            const div = document.createElement("div");
            div.className = "review-card";
            div.innerHTML = `
                <h3>${r.title}</h3>
                <p><strong>Type:</strong> ${r.type}</p>
                <p><strong>Rating:</strong> ${r.rating}/10</p>
                <p>${r.review}</p>
                <hr>
            `;
            container.appendChild(div);
        }
    }
});
