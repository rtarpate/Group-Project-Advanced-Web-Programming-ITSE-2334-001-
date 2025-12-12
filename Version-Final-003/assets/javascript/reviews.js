// ===============================================
// reviews.js - Hosting-Ready Version
// ===============================================

document.addEventListener("DOMContentLoaded", () => {

    const container = document.getElementById("reviews-container");
    const searchInput = document.getElementById("search-bar");
    const typeFilter = document.getElementById("type-filter");
    const genreFilter = document.getElementById("genre-filter");
    const ratingFilter = document.getElementById("rating-filter");
    const sortFilter = document.getElementById("sort-filter");
    const resetBtn = document.getElementById("reset-filters");

    if (!container) {
        console.warn("reviews.js: #reviews-container not found.");
        return;
    }

    let mediaList = [];

    function renderMedia(list) {
        container.innerHTML = "";

        if (!list.length) {
            container.innerHTML = "<p>No media found.</p>";
            return;
        }

        list.forEach(item => {
            const card = document.createElement("article");
            card.className = "media-card";

            const imgSrc = item.media_image
                ? `/assets/images/${item.media_image}`
                : "/assets/images/no-image.png";

            const avgRating = item.average_rating ?? 0;
            const reviewCount = item.review_count ?? 0;

            card.innerHTML = `
                <img src="${imgSrc}" alt="${item.media_title}" class="media-image">

                <div class="media-info">
                    <h3>${item.media_title}</h3>

                    <p class="media-type">${item.media_type}</p>

                    <p class="media-description">
                        Genre: ${item.genre_name ?? "N/A"}<br>
                        Content Rating: ${item.content_rating ?? "N/A"}
                    </p>

                    <p class="media-rating">
                        Average Rating: ${avgRating.toFixed(1)} / 10
                        (${reviewCount} review${reviewCount === 1 ? "" : "s"})
                    </p>

                    <a href="/index.php?page=media-details&media_id=${item.media_id}"
                       class="btn-details">
                       View Details
                    </a>
                </div>
            `;

            container.appendChild(card);
        });
    }

    function applyFilters() {
        const query = searchInput?.value.trim().toLowerCase() || "";
        const typeVal = typeFilter?.value || "all";
        const genreVal = genreFilter?.value || "all";
        const ratingVal = ratingFilter?.value || "all";
        const sortVal = sortFilter?.value || "title-asc";

        let filtered = mediaList.filter(item => {
            return (
                item.media_title.toLowerCase().includes(query) &&
                (typeVal === "all" || item.media_type === typeVal) &&
                (genreVal === "all" || item.genre_name === genreVal) &&
                (ratingVal === "all" || item.content_rating === ratingVal)
            );
        });

        switch (sortVal) {
            case "title-desc":
                filtered.sort((a, b) => b.media_title.localeCompare(a.media_title));
                break;
            case "rating-desc":
                filtered.sort((a, b) => (b.average_rating ?? 0) - (a.average_rating ?? 0));
                break;
            case "rating-asc":
                filtered.sort((a, b) => (a.average_rating ?? 0) - (b.average_rating ?? 0));
                break;
            case "newest":
                filtered.sort((a, b) => new Date(b.release_date) - new Date(a.release_date));
                break;
            case "oldest":
                filtered.sort((a, b) => new Date(a.release_date) - new Date(b.release_date));
                break;
            case "id-desc":
                filtered.sort((a, b) => b.media_id - a.media_id);
                break;
            case "id-asc":
                filtered.sort((a, b) => a.media_id - b.media_id);
                break;
            default:
                filtered.sort((a, b) => a.media_title.localeCompare(b.media_title));
                break;
        }

        renderMedia(filtered);
    }

    function loadMedia() {
        fetch("/router/router.php?action=getMedia")
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error("Invalid response");
                mediaList = data.media;
                applyFilters();
            })
            .catch(err => {
                console.error("reviews.js error:", err);
                container.innerHTML = "<p>Error loading media. Please try again later.</p>";
            });
    }

    if (searchInput) searchInput.addEventListener("input", applyFilters);
    if (typeFilter) typeFilter.addEventListener("change", applyFilters);
    if (genreFilter) genreFilter.addEventListener("change", applyFilters);
    if (ratingFilter) ratingFilter.addEventListener("change", applyFilters);
    if (sortFilter) sortFilter.addEventListener("change", applyFilters);

    if (resetBtn) {
        resetBtn.addEventListener("click", () => {
            searchInput.value = "";
            typeFilter.value = "all";
            genreFilter.value = "all";
            ratingFilter.value = "all";
            sortFilter.value = "title-asc";
            applyFilters();
        });
    }

    loadMedia();
});
