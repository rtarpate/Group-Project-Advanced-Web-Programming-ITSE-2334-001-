document.addEventListener("DOMContentLoaded", () => {
    debugMessage("JS Loaded. Starting fetch...\n");
    loadReviews();
});

function debugMessage(msg) {
    const debugBox = document.getElementById("debug");
    debugBox.textContent += msg + "\n";
}

function loadReviews() {
    // FIXED FETCH PATH — relative to reviews.html not JS file
    debugMessage("Fetching: get-reviews.php");

    fetch("get-reviews.php")
        .then(async response => {
            debugMessage("HTTP Status: " + response.status);

            const raw = await response.text();
            debugMessage("Raw Response:\n" + raw + "\n");

            let json;
            try {
                json = JSON.parse(raw);
            } catch (e) {
                debugMessage("❌ JSON Parse Error: " + e);
                return;
            }

            debugMessage("Parsed JSON:\n" + JSON.stringify(json, null, 2));

            if (json.status !== "success") {
                debugMessage("❌ API Error: " + json.message);
                return;
            }

            const container = document.getElementById("reviews-container");
            container.innerHTML = "";

            json.data.forEach(item => {

                let imageFile = item.image_path && item.image_path.trim() !== ""
                    ? item.image_path
                    : "no-image.jpg";

                const card = document.createElement("div");
                card.classList.add("review-card");

                card.innerHTML = `
                    <div class="review-image">
                        <img src="../assets/images/${item.image_path}"
                             alt="${item.title}"
                             onerror="this.src='../assets/images/no-image.jpg';" />
                    </div>

                    <div class="review-details">
                        <h2>${item.title}</h2>
                        <p><strong>Genre:</strong> ${item.genre}</p>
                        <p><strong>Type:</strong> ${item.media_type.replace(/_/g, ' ')}</p>
                        <p><strong>Rating:</strong> ${item.average_rating} / 10 (${item.total_ratings} ratings)</p>
                        <p><strong>Release Date:</strong> ${item.release_date}</p>
                    </div>
                `;

                container.appendChild(card);
            });

            debugMessage("Rendering complete.");
        })
        .catch(err => {
            debugMessage("❌ FETCH ERROR: " + err);
        });
}
