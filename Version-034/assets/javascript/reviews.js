// =====================================================
// reviews.js - Dynamic reviews listing with filters
// =====================================================

document.addEventListener("DOMContentLoaded", () => {

    const container   = document.getElementById("reviews-container");
    const searchInput = document.getElementById("search-bar");
    const typeFilter  = document.getElementById("type-filter");
    const sortFilter  = document.getElementById("sort-filter");

    if (!container) {
        return;
    }

    let mediaList = [];

    function normalizeType(str) {
        if (!str) return "";
        return str.toLowerCase().replace(/\s+/g, "");
    }

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
                ? `../assets/images/${item.media_image}`
                : "../assets/images/no-image.jpg";

            const avgRating = item.average_rating != null ? item.average_rating : 0;
            const reviewCount = item.review_count != null ? item.review_count : 0;

            card.innerHTML = `
                <img src="${imgSrc}" alt="${item.media_title}" class="media-image">
                <div class="media-info">
                    <h3>${item.media_title}</h3>
                    <p class="media-type">${item.media_type}</p>
                    <p class="media-description">${item.media_description}</p>
                    <p class="media-rating">
                        Average Rating: ${avgRating} / 10 (${reviewCount} reviews)
                    </p>
                    <a href="media-details.php?media_id=${item.media_id}" class="btn-details">
                        View Details
                    </a>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function loadMedia() {
        fetch("/groupproject/Version-033/router/router.php?action=getMedia")
            .then(response => response.json())
            .then(data => {
                if (!data.success || !Array.isArray(data.media)) {
                    throw new Error("Invalid data from server");
                }
                mediaList = data.media;
                applyFilters();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = "<p>Error loading media. Please try again later.</p>";
            });
    }

    function applyFilters() {
        const query = (searchInput?.value || "").trim().toLowerCase();
        const type  = typeFilter?.value || "all";
        const sort  = sortFilter?.value || "title-asc";

        let filtered = mediaList.filter(item => {
            const titleMatch = item.media_title.toLowerCase().includes(query);
            const descMatch  = item.media_description.toLowerCase().includes(query);

            let typeMatch = true;
            if (type !== "all" && type !== "") {
                typeMatch = normalizeType(item.media_type) === normalizeType(type);
            }

            return (titleMatch || descMatch) && typeMatch;
        });

        switch (sort) {
            case "title-asc":
                filtered.sort((a, b) => a.media_title.localeCompare(b.media_title));
                break;
            case "title-desc":
                filtered.sort((a, b) => b.media_title.localeCompare(a.media_title));
                break;
            case "rating-desc":
                filtered.sort((a, b) => (b.average_rating ?? 0) - (a.average_rating ?? 0));
                break;
            case "rating-asc":
                filtered.sort((a, b) => (a.average_rating ?? 0) - (b.average_rating ?? 0));
                break;
        }

        renderMedia(filtered);
    }

    if (searchInput) searchInput.addEventListener("input", applyFilters);
    if (typeFilter)  typeFilter.addEventListener("change", applyFilters);
    if (sortFilter)  sortFilter.addEventListener("change", applyFilters);

    loadMedia();
});
