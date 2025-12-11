// =====================================================
// reviews.js - FINAL VERSION with Type Normalization
// =====================================================

document.addEventListener("DOMContentLoaded", () => {

    const container   = document.getElementById("reviews-container");
    const searchInput = document.getElementById("search-bar");
    const typeFilter  = document.getElementById("type-filter");
    const sortFilter  = document.getElementById("sort-filter");

    let mediaList = [];

    // ----------------------------------------------------
    // Normalize media type for comparison
    // Removes spaces and converts to lowercase
    // ----------------------------------------------------
    function normalizeType(str) {
        return str.toLowerCase().replace(/\s+/g, '').trim();
    }

    // ----------------------------------------------------
    // Load all media from router.php
    // ----------------------------------------------------
    function loadMedia() {
        fetch("router.php?action=getMedia")
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    console.error("Router returned error:", data.error);
                    container.innerHTML = "<p>Error loading media.</p>";
                    return;
                }

                mediaList = data.media;
                renderMedia(mediaList);
            })
            .catch(err => {
                console.error("Network error:", err);
                container.innerHTML = "<p>Unable to load media.</p>";
            });
    }

    // ----------------------------------------------------
    // Render media cards
    // ----------------------------------------------------
    function renderMedia(list) {
        container.innerHTML = "";

        if (!list.length) {
            container.innerHTML = "<p>No media found.</p>";
            return;
        }

        list.forEach(media => {
            const card = document.createElement("div");
            card.className = "review-card";

            const imgFile = media.image_path ? media.image_path : "no-image.png";

            card.innerHTML = `
                <div class="review-image">
                    <img src="../assets/images/${imgFile}" alt="${media.title}">
                </div>

                <div class="review-details">
                    <h2>${media.title}</h2>
                    <p><strong>Genre:</strong> ${media.genre || "Unknown"}</p>
                    <p><strong>Type:</strong> ${media.media_type}</p>
                    <p><strong>Rating:</strong> ${media.average_rating}/10 (${media.total_ratings})</p>

                    <button 
                        class="more-info-btn"
                        onclick="window.location.href='media-details.html?media_id=${media.media_id}'">
                        More Info
                    </button>
                </div>
            `;

            container.appendChild(card);
        });
    }

    // ----------------------------------------------------
    // Unified Search + Filter + Sort
    // ----------------------------------------------------
    function applyFilters() {
        let filtered = [...mediaList];

        const search = searchInput.value.toLowerCase();
        const type = typeFilter.value;
        const sort = sortFilter.value;

        // Search by title
        if (search !== "") {
            filtered = filtered.filter(m =>
                m.title.toLowerCase().includes(search)
            );
        }

        // Filter by media type (normalized)
        if (type !== "") {
            filtered = filtered.filter(m =>
                normalizeType(m.media_type) === normalizeType(type)
            );
        }

        // Sorting
        switch (sort) {
            case "title-asc":
                filtered.sort((a, b) => a.title.localeCompare(b.title));
                break;

            case "title-desc":
                filtered.sort((a, b) => b.title.localeCompare(a.title));
                break;

            case "rating-desc":
                filtered.sort((a, b) => b.average_rating - a.average_rating);
                break;

            case "rating-asc":
                filtered.sort((a, b) => a.average_rating - b.average_rating);
                break;
        }

        renderMedia(filtered);
    }

    // ----------------------------------------------------
    // Event Listener Bindings
    // ----------------------------------------------------
    searchInput.addEventListener("input", applyFilters);
    typeFilter.addEventListener("change", applyFilters);
    sortFilter.addEventListener("change", applyFilters);

    // Load the full media list on page load
    loadMedia();
});


// ======================================================
// DEBUG LOGGER
// ======================================================
function logDebug(msg) {
    console.log("[DEBUG]", msg);
    const panel = document.getElementById("debug-panel");
    if (panel) {
        panel.innerHTML += msg + "<br>";
    }
}

// ======================================================
// MAIN SCRIPT
// ======================================================

document.addEventListener("DOMContentLoaded", () => {
    const container   = document.getElementById("reviews-container");
    const searchInput = document.getElementById("search-bar");
    const typeFilter  = document.getElementById("type-filter");
    const sortFilter  = document.getElementById("sort-filter");

    let mediaList = [];

    // Cleaner string normalize
    function normalizeType(str) {
        return str.toLowerCase().replace(/[\s_]+/g, '').trim();
    }

    // Try loading media from router
    function loadMedia() {
        logDebug("Fetching: router.php?action=getMedia");

        fetch("router.php?action=getMedia")
            .then(res => {
                logDebug("HTTP Status: " + res.status);

                if (!res.ok) {
                    logDebug("ERROR: Response not OK. Check router.php path.");
                }

                return res.text();
            })
            .then(text => {
                logDebug("Raw Response:");
                logDebug(text.replace(/</g, "&lt;"));

                let data;

                try {
                    data = JSON.parse(text);
                } catch (err) {
                    logDebug("❌ JSON PARSE ERROR");
                    logDebug(err.toString());
                    return;
                }

                if (!data.success) {
                    logDebug("❌ Router returned error: " + data.error);
                    return;
                }

                logDebug("Media loaded: " + data.media.length + " items");

                mediaList = data.media;
                renderMedia(mediaList);
            })
            .catch(err => {
                logDebug("❌ FETCH ERROR:");
                logDebug(err.toString());
            });
    }

    // Render media
    function renderMedia(list) {
        container.innerHTML = "";

        if (!list.length) {
            container.innerHTML = "<p>No media found.</p>";
            logDebug("Displayed: No media found.");
            return;
        }

        list.forEach(m => {
            const card = document.createElement("div");
            card.className = "review-card";

            const img = m.image_path ? m.image_path : "no-image.png";

            card.innerHTML = `
                <img class="media-image" src="../assets/images/${img}" alt="${m.title}">
                <h2>${m.title}</h2>
                <p><strong>Type:</strong> ${m.media_type}</p>
                <p><strong>Genre:</strong> ${m.genre}</p>
                <p><strong>Rating:</strong> ${m.average_rating}/10 (${m.total_ratings})</p>
                <button class="more-info-btn"
                    onclick="window.location.href='media-details.html?media_id=${m.media_id}'">
                    More Info
                </button>
            `;

            container.appendChild(card);
        });

        logDebug("Rendered " + list.length + " media cards.");
    }

    // Apply filters
    function applyFilters() {
        let filtered = [...mediaList];

        const search = searchInput.value.toLowerCase();
        const type   = typeFilter.value;
        const sort   = sortFilter.value;

        logDebug("Applying filters. Search='" + search + "', Type='" + type + "', Sort='" + sort + "'");

        if (search !== "") {
            filtered = filtered.filter(m =>
                m.title.toLowerCase().includes(search)
            );
        }

        if (type !== "") {
            filtered = filtered.filter(m =>
                normalizeType(m.media_type) === normalizeType(type)
            );
        }

        switch (sort) {
            case "title-asc":  filtered.sort((a,b)=>a.title.localeCompare(b.title)); break;
            case "title-desc": filtered.sort((a,b)=>b.title.localeCompare(a.title)); break;
            case "rating-desc": filtered.sort((a,b)=>b.average_rating - a.average_rating); break;
            case "rating-asc": filtered.sort((a,b)=>a.average_rating - b.average_rating); break;
        }

        renderMedia(filtered);
    }

    searchInput.addEventListener("input", applyFilters);
    typeFilter.addEventListener("change", applyFilters);
    sortFilter.addEventListener("change", applyFilters);

    loadMedia();
});
