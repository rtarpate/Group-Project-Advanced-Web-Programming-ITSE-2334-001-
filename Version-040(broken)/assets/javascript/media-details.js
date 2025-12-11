// =====================================================
// media-details.js - Load information for a single media
// =====================================================

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const mediaId = urlParams.get("media_id");

    if (!mediaId) return;

    const detailsContainer = document.getElementById("media-details");
    const reviewsContainer = document.getElementById("reviews-list");

    // ---------------------------------------
    // Load media info
    // ---------------------------------------
    fetch(`../router/router.php?action=getMediaDetails&media_id=${mediaId}`)
        .then(res => {
            if (!res.ok) {
                throw new Error("Network response was not ok");
            }
            return res.json();
        })
        .then(data => {
            if (!data.success || !data.media) {
                detailsContainer.innerHTML = "<p>Media not found.</p>";
                return;
            }

            const m = data.media;

            detailsContainer.innerHTML = `
                <div class="media-details-layout">

                    <div class="media-details-poster">
                        <img src="../assets/images/${m.image_path || "0.jpg"}" alt="${m.title}">
                    </div>

                    <div class="media-details-info">
                        <h1>${m.title}</h1>
                        <p><strong>Type:</strong> ${m.media_type || "Unknown"}</p>
                        <p><strong>Genre:</strong> ${m.genre || "Unknown"}</p>
                        <p><strong>Release Date:</strong> ${m.release_date || "N/A"}</p>
                        <p><strong>Director:</strong> ${m.director || "N/A"}</p>
                        <p><strong>Content Rating:</strong> ${m.content_rating || "N/A"} (${m.content_rating_desc || ""})</p>
                        <p><strong>Average Rating:</strong> ${m.average_rating ?? "No ratings yet"}</p>
                        <p><strong>Total Ratings:</strong> ${m.total_ratings ?? 0}</p>
                    </div>

                </div>
            `;
        })
        .catch(err => {
            console.error(err);
            detailsContainer.innerHTML = "<p>Error loading media details.</p>";
        });

    // ---------------------------------------
    // Load user reviews for this media
    // ---------------------------------------
    fetch(`../router/router.php?action=getMediaReviews&media_id=${mediaId}`)
        .then(res => {
            if (!res.ok) {
                throw new Error("Network response was not ok");
            }
            return res.json();
        })
        .then(data => {
            reviewsContainer.innerHTML = "";

            if (!data.success || !Array.isArray(data.reviews) || data.reviews.length === 0) {
                reviewsContainer.innerHTML = "<p>No reviews yet for this media.</p>";
                return;
            }

            data.reviews.forEach(r => {
                const div = document.createElement("div");
                div.className = "review-card";
                div.innerHTML = `
                    <p><strong>Rating:</strong> ${r.rating} / 10</p>
                    <p>${r.review_text || ""}</p>
                    <small>Posted: ${r.created_at}</small>
                `;
                reviewsContainer.appendChild(div);
            });
        })
        .catch(err => {
            console.error(err);
            reviewsContainer.innerHTML = "<p>Error loading reviews.</p>";
        });
});
