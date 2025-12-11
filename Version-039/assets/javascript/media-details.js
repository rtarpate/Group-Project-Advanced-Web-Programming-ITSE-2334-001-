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
    fetch(`/groupproject/Version-035/router/router.php?action=getMediaDetails&media_id=${mediaId}`)
        .then(res => res.json())
        .then(data => {
            console.log("DETAILS:", data);

            if (!data.success) throw new Error(data.error);

            const m = data.media;

            const imgSrc = m.media_image
                ? `../assets/images/${m.media_image}`
                : "../assets/images/no-image.jpg";

            detailsContainer.innerHTML = `
                <img src="${imgSrc}" class="details-image">
                
                <h2>${m.media_title}</h2>
                <p><strong>Director:</strong> ${m.director}</p>
                <p><strong>Release:</strong> ${m.release_date}</p>
                <p><strong>Genre:</strong> ${m.genre_name}</p>
                <p><strong>Content Rating:</strong> ${m.content_rating}</p>
                <p><strong>Average Rating:</strong> ${m.average_rating} / 10</p>
            `;
        })
        .catch(err => {
            console.error(err);
            detailsContainer.innerHTML = "<p>Error loading media details.</p>";
        });

    // ---------------------------------------
    // Load reviews
    // ---------------------------------------
    fetch(`/groupproject/Version-035/router/router.php?action=getMediaReviews&media_id=${mediaId}`)
        .then(res => res.json())
        .then(data => {
            console.log("REVIEWS:", data);

            if (!data.success) throw new Error(data.error);

            if (!data.reviews.length) {
                reviewsContainer.innerHTML = "<p>No reviews yet.</p>";
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
