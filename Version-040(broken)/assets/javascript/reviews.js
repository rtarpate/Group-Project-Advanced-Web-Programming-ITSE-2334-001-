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

    // ---------------------------------------
    // Load all media w/ average ratings
    // ---------------------------------------
    async function loadMedia() {
        try {
            const res = await fetch("../router/router.php?action=getMedia");
            if (!res.ok) {
                throw new Error("Network response was not ok");
            }

            const data = await res.json();

            if (!data.success || !Array.isArray(data.media)) {
                container.innerHTML = "<p>Error loading media list.</p>";
                return;
            }

            mediaList = data.media;
            renderMedia(mediaList);

        } catch (err) {
            console.error(err);
            container.innerHTML = "<p>Error loading media list.</p>";
        }
    }

    // ---------------------------------------
    // Render media cards
    // ---------------------------------------
    function renderMedia(list) {
        container.innerHTML = "";

        if (!list.length) {
            container.innerHTML = "<p>No media found.</p>";
            return;
        }

        list.forEach(m => {
            const card = document.createElement("div");
            card.className = "media-card";

            card.innerHTML = `
                <div class="media-card-image">
                    <img src="../assets/images/${m.image_path || "0.jpg"}" alt="${m.title}">
                </div>
                <div class="media-card-body">
                    <h2>${m.title}</h2>
                    <p><strong>Type:</strong> ${m.media_type || "Unknown"}</p>
                    <p><strong>Average Rating:</strong> ${m.average_rating ?? "No ratings yet"}</p>
                    <p><strong>Total Ratings:</strong> ${m.total_ratings ?? 0}</p>
                    <a href="../pages/media-details.php?media_id=${m.media_id}" class="btn-details">View Details</a>
                </div>
            `;

            container.appendChild(card);
        });
    }

    // ---------------------------------------
    // Filtering + Sorting
    // ---------------------------------------
    function applyFilters() {
        let filtered = [...mediaList];

        const searchTerm = (searchInput?.value || "").toLowerCase();
        const typeValue  = typeFilter?.value || "";
        const sortValue  = sortFilter?.value || "";

        if (searchTerm) {
            filtered = filtered.filter(m =>
                (m.title || "").toLowerCase().includes(searchTerm)
            );
        }

        if (typeValue) {
            filtered = filtered.filter(m => (m.media_type || "") === typeValue);
        }

        switch (sortValue) {
            case "title-asc":
                filtered.sort((a, b) => (a.title || "").localeCompare(b.title || ""));
                break;
            case "title-desc":
                filtered.sort((a, b) => (b.title || "").localeCompare(a.title || ""));
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
