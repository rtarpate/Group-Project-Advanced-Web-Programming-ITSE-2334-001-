// ================================================
// media-details.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const mediaId = params.get("media_id");

    if (!mediaId) return;

    // Load media details
    fetch(`pages/router.php?action=getMediaDetails&media_id=${mediaId}`)
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) return;

            const media = data.media;

            document.getElementById("media-title").textContent = media.title;
            document.getElementById("media-image").src = `assets/images/${media.image_path}`;
            document.getElementById("media-genre").textContent = media.genre;
            document.getElementById("media-rating").textContent = media.content_rating;
            document.getElementById("media-release").textContent = media.release_date;
            document.getElementById("media-director").textContent = media.director;
        });

    // Load all reviews
    fetch(`pages/router.php?action=getMediaReviews&media_id=${mediaId}`)
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) return;
            const list = document.getElementById("reviews-list");

            data.reviews.forEach((r) => {
                const div = document.createElement("div");
                div.className = "review-item";
                div.innerHTML = `
                    <strong>Rating:</strong> ${r.rating}/10<br>
                    <p>${r.review_text}</p>
                    <hr>
                `;
                list.appendChild(div);
            });
        });
});
