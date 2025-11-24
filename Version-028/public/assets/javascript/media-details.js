document.addEventListener("DOMContentLoaded", () => {

    // Get media_id from URL
    const params = new URLSearchParams(window.location.search);
    const mediaId = params.get("media_id");

    if (!mediaId) {
        document.body.innerHTML = "<h2>Invalid Media ID</h2>";
        return;
    }

    const titleEl = document.getElementById("media-title");
    const imageEl = document.getElementById("media-image");
    const starEl  = document.getElementById("media-stars");

    const avgEl   = document.getElementById("media-average");
    const countEl = document.getElementById("media-count");
    const genreEl = document.getElementById("media-genre");
    const typeEl  = document.getElementById("media-type");
    const releaseEl = document.getElementById("media-release");
    const contentEl = document.getElementById("media-content-rating");

    const reviewsList = document.getElementById("reviews-list");

    // ================================
    // STAR GENERATOR (same as reviews.js)
    // ================================
    function generateStars(rating) {
        const maxStars = 10;
        const fullStars = Math.floor(rating);
        const halfStar = (rating % 1) >= 0.5 ? 1 : 0;
        const emptyStars = maxStars - fullStars - halfStar;

        let html = "";

        for (let i = 0; i < fullStars; i++) html += `<span class="star full">★</span>`;
        if (halfStar) html += `<span class="star half">★</span>`;
        for (let i = 0; i < emptyStars; i++) html += `<span class="star empty">☆</span>`;

        return html;
    }

    // ================================
    // FETCH MEDIA DETAILS
    // ================================
    fetch(`router.php?action=getMediaDetails&media_id=${mediaId}`)
        .then(res => res.json())
        .then(json => {
            if (json.status !== "success") return;

            const m = json.data;

            titleEl.textContent = m.title;
            imageEl.src = `../assets/images/${m.image_path}`;
            avgEl.textContent = m.average_rating;
            countEl.textContent = m.total_ratings;
            genreEl.textContent = m.genre;
            typeEl.textContent = m.media_type;
            releaseEl.textContent = m.release_date;
            contentEl.textContent = m.content_rating;

            starEl.innerHTML = generateStars(m.average_rating);
        });

    // ================================
    // FETCH ALL USER REVIEWS
    // ================================
    fetch(`router.php?action=getMediaReviews&media_id=${mediaId}`)
        .then(res => res.json())
        .then(json => {

            if (!json.data || json.data.length === 0) {
                reviewsList.innerHTML = "<p>No reviews yet.</p>";
                return;
            }

            json.data.forEach(r => {
                const div = document.createElement("div");
                div.className = "review-item";

                div.innerHTML = `
                    <div class="review-rating">${generateStars(r.rating)}</div>
                    <p>${r.review_text || "(No written review)"}</p>
                    <small>${r.review_date}</small>
                `;

                reviewsList.appendChild(div);
            });
        });

});
