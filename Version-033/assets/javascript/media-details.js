// ================================================
// media-details.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const detailsContainer = document.getElementById("media-details");
    const reviewsContainer = document.getElementById("media-reviews");

    if (!detailsContainer || !reviewsContainer) return;

    const urlParams = new URLSearchParams(window.location.search);
    const mediaId   = urlParams.get("media_id");

    if (!mediaId) {
        detailsContainer.textContent = "No media selected.";
        return;
    }

    // Load media details
    fetch(`/groupproject/Version-033/router/router.php?action=getMediaDetails&media_id=${mediaId}`)
        .then((response) => response.json())
        .then((data) => {
            if (!data.success || !data.media) {
                detailsContainer.textContent = "Media not found.";
                return;
            }

            const m = data.media;

            detailsContainer.innerHTML = `
                <h2>${m.media_title}</h2>
                <p><strong>Type:</strong> ${m.media_type}</p>
                <p><strong>Description:</strong> ${m.media_description}</p>
                <p><strong>Average Rating:</strong> ${m.average_rating ?? 0} / 10 (${m.review_count ?? 0} reviews)</p>
            `;
        })
        .catch((err) => {
            console.error(err);
            detailsContainer.textContent = "Error loading media details.";
        });

    // Load media reviews
    fetch(`/groupproject/Version-033/router/router.php?action=getMediaReviews&media_id=${mediaId}`)
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                reviewsContainer.textContent = "Error loading reviews.";
                return;
            }

            const reviews = data.reviews || [];

            if (!reviews.length) {
                reviewsContainer.textContent = "No reviews yet.";
                return;
            }

            const list = document.createElement("ul");
            list.className = "review-list";

            reviews.forEach((rv) => {
                const li = document.createElement("li");
                li.innerHTML = `
                    <div class="review-rating">Rating: ${rv.rating} / 10</div>
                    <div class="review-text">${rv.review_text ? rv.review_text : "(No review text)"}</div>
                    <div class="review-date">${rv.created_at}</div>
                `;
                list.appendChild(li);
            });

            reviewsContainer.innerHTML = "";
            reviewsContainer.appendChild(list);
        })
        .catch((err) => {
            console.error(err);
            reviewsContainer.textContent = "Error loading reviews.";
        });
});
