// ================================================
// reviews.js (Router Path FIXED)
// ================================================

document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("reviews-container");
    const searchInput = document.getElementById("search-bar");
    const typeFilter = document.getElementById("type-filter");

    function loadReviews() {
        fetch("pages/router.php?action=getMedia")
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) return;

                const mediaList = data.media;

                container.innerHTML = "";

                mediaList.forEach((media) => {
                    const div = document.createElement("div");
                    div.className = "media-item";

                    div.innerHTML = `
                        <img src="assets/images/${media.image_path}" />
                        <h3>${media.title}</h3>
                        <p>${media.genre}</p>
                        <p>Rating: ${media.average_rating}/10</p>
                        <a href="media-details.html?media_id=${media.media_id}">More Info</a>
                    `;

                    container.appendChild(div);
                });
            });
    }

    loadReviews();
});
