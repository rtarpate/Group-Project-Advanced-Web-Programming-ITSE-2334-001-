// public/assets/javascript/reviews.js

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('reviews-container');
  const debugBox  = document.getElementById('debug');

  function logDebug(msg) {
    if (debugBox) {
      debugBox.textContent += msg + "\n";
    }
  }

  fetch('router.php?action=getMedia')
    .then(response => {
      logDebug("HTTP status: " + response.status);
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then(json => {
      logDebug("Response JSON: " + JSON.stringify(json, null, 2));

      if (json.status !== 'success') {
        throw new Error(json.message || "Unexpected API response");
      }

      const items = json.data || [];
      container.innerHTML = '';

      items.forEach(media => {
        const card = document.createElement('div');
        card.className = 'review-card';

        const imgSrc = `../assets/images/${media.image_path || 'no-image.jpg'}`;

        card.innerHTML = `
          <div class="review-image">
            <img src="${imgSrc}" alt="${media.title || 'Poster'}">
          </div>
          <div class="review-details">
            <h2>${media.title}</h2>
            <p><strong>Type:</strong> ${media.media_type}</p>
            <p><strong>Genre:</strong> ${media.genre}</p>
            <p><strong>Content Rating:</strong> ${media.content_rating}</p>
            <p><strong>Release Date:</strong> ${media.release_date}</p>
            <p><strong>Average Rating:</strong> ${media.average_rating} / 10
               (${media.total_ratings} ratings)</p>
          </div>
        `;

        container.appendChild(card);
      });
    })
    .catch(err => {
      logDebug("ERROR: " + err.message);
      if (container) {
        container.innerHTML = "<p>Sorry, we couldn't load the reviews.</p>";
      }
    });
});
