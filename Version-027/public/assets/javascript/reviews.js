document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('reviews-container');
  const debugBox  = document.getElementById('debug');

  const searchInput = document.getElementById('search-input');
  const sortSelect  = document.getElementById('sort-select');

  let allMedia = [];  // ALL MEDIA FROM SERVER
  let filteredMedia = []; // MEDIA AFTER SEARCH + SORT


  function logDebug(msg) {
    if (debugBox) {
      debugBox.textContent += msg + "\n";
    }
  }

  // ===============================================
  // STAR GENERATOR
  // ===============================================
  function generateStars(rating) {
      const maxStars = 10;
      const fullStars = Math.floor(rating);
      const halfStar = (rating % 1) >= 0.5 ? 1 : 0;
      const emptyStars = maxStars - fullStars - halfStar;

      let html = "";

      for (let i = 0; i < fullStars; i++) html += `<span class="star full">★</span>`;
      if (halfStar) html += `<span class="star half">★</span>`;
      for (let i = 0; i < emptyStars; i++) html += `<span class="star empty">☆</span>`;

      return html;
  }


  // ===============================================
  // RENDER MEDIA CARDS
  // ===============================================
  function renderMedia(mediaList) {
      container.innerHTML = "";

      if (mediaList.length === 0) {
          container.innerHTML = "<p>No media found.</p>";
          return;
      }

      mediaList.forEach(media => {
          const card = document.createElement('div');
          card.className = 'review-card';

          const imgSrc = `../assets/images/${media.image_path || 'no-image.jpg'}`;

          card.innerHTML = `
              <div class="review-image">
                  <img src="${imgSrc}" alt="${media.title}">
              </div>

              <div class="review-details">
                  <h2>${media.title}</h2>

                  <div class="stars">
                      ${generateStars(media.average_rating)}
                  </div>

                  <p><strong>Average Rating:</strong> ${media.average_rating} / 10</p>
                  <p><strong>Ratings:</strong> ${media.total_ratings}</p>
                  <p><strong>Type:</strong> ${media.media_type}</p>
                  <p><strong>Genre:</strong> ${media.genre}</p>
                  <p><strong>Release:</strong> ${media.release_date}</p>
              </div>
          `;

          container.appendChild(card);
      });
  }


  // ===============================================
  // SEARCH FILTER
  // ===============================================
  function applySearch() {
      const q = searchInput.value.toLowerCase();

      filteredMedia = allMedia.filter(media =>
         media.title.toLowerCase().includes(q) ||
         media.genre.toLowerCase().includes(q) ||
         media.media_type.toLowerCase().includes(q)
      );
  }


  // ===============================================
  // SORTING
  // ===============================================
  function applySort() {
      const val = sortSelect.value;

      switch(val) {
          case "rating-high":
              filteredMedia.sort((a, b) => b.average_rating - a.average_rating);
              break;

          case "rating-low":
              filteredMedia.sort((a, b) => a.average_rating - b.average_rating);
              break;

          case "title-az":
              filteredMedia.sort((a, b) => a.title.localeCompare(b.title));
              break;

          case "title-za":
              filteredMedia.sort((a, b) => b.title.localeCompare(a.title));
              break;

          case "most-ratings":
              filteredMedia.sort((a, b) => b.total_ratings - a.total_ratings);
              break;

          case "release-new":
              filteredMedia.sort((a, b) => new Date(b.release_date) - new Date(a.release_date));
              break;

          case "release-old":
              filteredMedia.sort((a, b) => new Date(a.release_date) - new Date(b.release_date));
              break;

          default:
              // no sorting
              break;
      }
  }


  // ===============================================
  // APPLY SEARCH + SORT TOGETHER
  // ===============================================
  function updateDisplay() {
      applySearch();
      applySort();
      renderMedia(filteredMedia);
  }


  // ===============================================
  // FETCH MEDIA FROM SERVER
  // ===============================================
  fetch("router.php?action=getMedia")
    .then(response => response.json())
    .then(json => {
        allMedia = json.data || [];
        filteredMedia = [...allMedia];
        renderMedia(filteredMedia);
    });


  // LISTENERS
  searchInput.addEventListener("input", updateDisplay);
  sortSelect.addEventListener("change", updateDisplay);
});
