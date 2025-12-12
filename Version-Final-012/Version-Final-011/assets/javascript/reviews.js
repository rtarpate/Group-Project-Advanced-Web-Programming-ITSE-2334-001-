document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("mediaContainer");

    fetch("/router/router.php?action=getMedia")
        .then(response => response.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.media)) {
                container.innerHTML = "<p>No media found.</p>";
                return;
            }

            container.innerHTML = "";

            data.media.forEach(item => {
                const card = document.createElement("div");
                card.className = "media-card";

                card.innerHTML = `
                    <img src="/assets/images/${item.media_image || 'no-image.jpg'}" alt="${item.media_title}">
                    <h3>${item.media_title}</h3>
                    <p><strong>Type:</strong> ${item.media_type}</p>
                    <p><strong>Genre:</strong> ${item.genre_name ?? 'N/A'}</p>
                    <p><strong>Rating:</strong> ${item.average_rating ?? '0.0'}</p>
                `;

                container.appendChild(card);
            });
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = "<p>Error loading media.</p>";
        });
});
